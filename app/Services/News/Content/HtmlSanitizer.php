<?php

namespace App\Services\News\Content;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Allowlist HTML sanitiser for AI generated article bodies. The output is re-serialised
 * from the DOM, so nothing that is not explicitly allowed can survive: no scripts,
 * styles, iframes, event handlers or non-http(s) links.
 */
class HtmlSanitizer
{
    private const ALLOWED = ['p', 'h2', 'h3', 'ul', 'ol', 'li', 'strong', 'em', 'blockquote', 'a', 'img', 'figure', 'figcaption', 'br'];

    /** Elements removed together with everything inside them. */
    private const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet', 'noscript', 'template',
        'svg', 'math', 'form', 'textarea', 'select', 'button', 'input', 'link', 'meta', 'head', 'title', 'base',
    ];

    /** Tags rewritten to an allowed equivalent instead of being unwrapped. */
    private const RENAME = ['h1' => 'h2', 'h4' => 'h3', 'h5' => 'h3', 'h6' => 'h3', 'b' => 'strong', 'i' => 'em'];

    private const BLOCKS = ['p', 'h2', 'h3', 'ul', 'ol', 'li', 'blockquote', 'figure'];

    /** @param  list<string>  $internalHosts  hosts whose links are not treated as external */
    public function __construct(private readonly array $internalHosts = []) {}

    public function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $doc = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $doc->getElementsByTagName('body')->item(0);
        if (! $body) {
            return '';
        }

        $out = $this->children($body);
        // Drop paragraphs and headings that ended up empty after stripping.
        $out = preg_replace('#<(p|h2|h3|li|blockquote)>\s*</\1>\n?#u', '', $out) ?? $out;

        return trim($out);
    }

    private function children(DOMNode $node): string
    {
        $out = '';
        foreach (iterator_to_array($node->childNodes) as $child) {
            $out .= $this->render($child);
        }

        return $out;
    }

    private function render(DOMNode $node): string
    {
        if ($node instanceof DOMText) {
            return htmlspecialchars((string) $node->nodeValue, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }
        if (! $node instanceof DOMElement) {
            return ''; // comments, processing instructions, CDATA
        }

        $tag = strtolower($node->tagName);
        if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
            return '';
        }
        $tag = self::RENAME[$tag] ?? $tag;

        if (! in_array($tag, self::ALLOWED, true)) {
            return $this->children($node); // unknown wrapper: keep its text
        }

        return match ($tag) {
            'a' => $this->link($node),
            'img' => $this->image($node),
            'br' => '<br>',
            default => "<{$tag}>".$this->children($node)."</{$tag}>".(in_array($tag, self::BLOCKS, true) ? "\n" : ''),
        };
    }

    private function link(DOMElement $a): string
    {
        $href = $this->cleanUrl($a->getAttribute('href'));
        $inner = $this->children($a);

        if ($href === null) {
            return $inner;
        }

        $attrs = ' href="'.htmlspecialchars($href, ENT_QUOTES, 'UTF-8').'"';
        if ($this->isExternal($href)) {
            $attrs .= ' target="_blank" rel="nofollow noopener"';
        }

        return "<a{$attrs}>{$inner}</a>";
    }

    private function image(DOMElement $img): string
    {
        $src = $this->cleanUrl($img->getAttribute('src'));
        if ($src === null) {
            return '';
        }
        $alt = htmlspecialchars($img->getAttribute('alt'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<img src="'.htmlspecialchars($src, ENT_QUOTES, 'UTF-8').'" alt="'.$alt.'" loading="lazy">';
    }

    /** Only absolute http(s) URLs and root-relative paths survive. Everything else (javascript:, data:, //host) is refused. */
    private function cleanUrl(string $url): ?string
    {
        $url = preg_replace('/[\x00-\x20\x7F]+/u', '', $url) ?? '';

        if (preg_match('~^https?://[^/?#]+~i', $url)) {
            return $url;
        }
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        return null;
    }

    private function isExternal(string $url): bool
    {
        if (! preg_match('#^https?://#i', $url)) {
            return false;
        }

        return ! in_array(strtolower((string) parse_url($url, PHP_URL_HOST)), array_map('strtolower', $this->internalHosts), true);
    }
}
