<?php

namespace App\Services\News\Writing;

use RuntimeException;

/** The writer could not produce a usable answer (API error, truncated or malformed output). Scoped to one item. */
class ArticleWriterException extends RuntimeException {}
