import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    ChevronLeft, ChevronRight, Calendar, Clock, Eye, Star, Share2,
    ArrowLeft, Copy, Check, Facebook, Twitter, Linkedin, Link2
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Avatar } from '@/components/ui/avatar';
import Navbar from '@/components/Navbar';
import hljs from 'highlight.js';
import 'highlight.js/styles/github.css';
import 'highlight.js/styles/github-dark.css';

// CSS tùy chỉnh cho nội dung TinyMCE
const customStyles = `
.dark .prose-dark img {
    filter: brightness(0.8);
    border-radius: 0.5rem;
    border: 1px solid rgba(55, 65, 81, 0.2);
}

.prose img {
    border-radius: 0.5rem;
    border: 1px solid rgba(229, 231, 235, 0.5);
    transition: all 0.3s ease;
    margin: 1.5rem auto;
    display: block;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.prose img:hover {
    transform: scale(1.01);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.dark .prose img:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
}

.prose figure {
    margin: 2rem auto;
}

.prose figure figcaption {
    text-align: center;
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.dark .prose figure figcaption {
    color: #9ca3af;
}

/* Cải thiện hiển thị hình ảnh được thêm class content-image */
.content-image {
    display: block;
    max-width: 100%;
    height: auto;
    margin: 1.5rem auto;
    border-radius: 0.5rem;
    border: 1px solid rgba(229, 231, 235, 0.5);
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.content-image:hover {
    transform: scale(1.01);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.dark .content-image {
    filter: brightness(0.9);
    border-color: rgba(55, 65, 81, 0.2);
}

.dark .content-image:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
}

/* Thêm responsive cho hình ảnh lớn */
@media (max-width: 768px) {
    .content-image, .prose img {
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
    }
}

/* Thêm responsive cho figure */
figure {
    max-width: 100%;
    overflow-x: auto;
}

.dark .prose-dark iframe {
    border-color: #4b5563 !important;
}

.dark .prose-dark div[data-oembed] {
    background-color: #1f2937;
    border-color: #374151;
}

.dark .mce-content-body {
    color: #e5e7eb !important;
}

.dark .prose-dark table {
    border-color: #4b5563;
}

.dark .prose-dark table th,
.dark .prose-dark table td {
    border-color: #4b5563;
}

.dark .prose-dark code:not(pre code) {
    background-color: #374151;
    color: #f472b6;
}

/* Nút copy code */
.code-block-header {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

pre {
    padding-top: 2.5rem !important;
    position: relative;
}

.hljs {
    background: transparent !important;
    padding: 0 !important;
}

/* Light mode */
html:not(.dark) .hljs {
    display: block;
}

/* Dark mode */
html.dark .hljs {
    display: none;
}

html.dark .hljs-dark {
    display: block;
}

html:not(.dark) .hljs-dark {
    display: none;
}

/* Thêm label ngôn ngữ lập trình */
.code-language-label {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    font-size: 0.75rem;
    font-family: monospace;
    color: #6b7280;
    background-color: rgba(243, 244, 246, 0.7);
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    text-transform: uppercase;
    z-index: 10;
}

.dark .code-language-label {
    background-color: rgba(31, 41, 55, 0.7);
    color: #9ca3af;
}

/* Đường viền và trang trí cho khối code */
pre {
    border: 1px solid rgba(209, 213, 219, 0.5) !important;
}

.dark pre {
    border: 1px solid rgba(55, 65, 81, 0.5) !important;
}
`;

interface BlogPost {
    id: number;
    title: string;
    slug: string;
    body: string;
    excerpt: string;
    featured_image?: string;
    thumbnail_url?: string;
    categories: string[];
    tags: string[];
    type: string;
    author: {
        name: string;
        avatar: string;
        bio?: string;
    };
    date: string;
    reading_time: number;
    stars: number;
    views: number;
}

interface RelatedPost {
    id: number;
    slug: string;
    title: string;
    excerpt: string;
    thumbnail_url?: string;
    date: string;
}

interface NavigationPost {
    id: number;
    slug: string;
    title: string;
}

interface PageProps {
    blog: BlogPost;
    latestBlogs: RelatedPost[];
    relatedBlogs: RelatedPost[];
    navigation: {
        previous: NavigationPost | null;
        next: NavigationPost | null;
    };
    error?: string;
}

// Map backend types to Vietnamese display values
const typeMapping: Record<string, string> = {
    'article': 'Bài viết',
    'news': 'Tin tức',
    'trick': 'Mẹo'
};

// Hàm tạo slug từ tên category
const createSlug = (text: string): string => {
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd').replace(/Đ/g, 'D')
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .trim();
};

const getTypeColor = (type: string) => {
    switch (type) {
        case 'Bài viết':
        case 'article':
            return {
                light: 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100',
                dark: 'dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40'
            };
        case 'Tin tức':
        case 'news':
            return {
                light: 'bg-purple-50 text-purple-600 border-purple-200 hover:bg-purple-100',
                dark: 'dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800/50 dark:hover:bg-purple-900/40'
            };
        case 'Mẹo':
        case 'trick':
            return {
                light: 'bg-amber-50 text-amber-600 border-amber-200 hover:bg-amber-100',
                dark: 'dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50 dark:hover:bg-amber-900/40'
            };
        default:
            return {
                light: 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100',
                dark: 'dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600'
            };
    }
};

type TagColors = {
    [key: string]: {
        light: string;
        dark: string;
    };
};

const getTagColor = (tag: string) => {
    const colors: TagColors = {
        'React': {
            light: 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100',
            dark: 'dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40'
        },
        'Node.js': {
            light: 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100',
            dark: 'dark:bg-green-900/30 dark:text-green-400 dark:border-green-800/50 dark:hover:bg-green-900/40'
        },
        'TailwindCSS': {
            light: 'bg-cyan-50 text-cyan-600 border-cyan-200 hover:bg-cyan-100',
            dark: 'dark:bg-cyan-900/30 dark:text-cyan-400 dark:border-cyan-800/50 dark:hover:bg-cyan-900/40'
        },
        'Laravel': {
            light: 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100',
            dark: 'dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/50 dark:hover:bg-red-900/40'
        },
        'Frontend': {
            light: 'bg-purple-50 text-purple-600 border-purple-200 hover:bg-purple-100',
            dark: 'dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800/50 dark:hover:bg-purple-900/40'
        },
        'Backend': {
            light: 'bg-orange-50 text-orange-600 border-orange-200 hover:bg-orange-100',
            dark: 'dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800/50 dark:hover:bg-orange-900/40'
        },
        'FullStack': {
            light: 'bg-indigo-50 text-indigo-600 border-indigo-200 hover:bg-indigo-100',
            dark: 'dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800/50 dark:hover:bg-indigo-900/40'
        }
    };
    return colors[tag] || {
        light: 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100',
        dark: 'dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600'
    };
};

const BlogDetail: React.FC = () => {
    const { blog, latestBlogs, relatedBlogs, navigation, error } = usePage().props as unknown as PageProps;
    const [showShareModal, setShowShareModal] = useState(false);
    const [processedContent, setProcessedContent] = useState(blog?.body || '');
    const [copyStatus, setCopyStatus] = useState<Record<string, boolean>>({});
    const [linkCopied, setLinkCopied] = useState(false);
    const blogContentRef = useRef<HTMLDivElement>(null);

    // Áp dụng style tùy chỉnh
    useEffect(() => {
        // Thêm style vào head
        const styleElement = document.createElement('style');
        styleElement.textContent = customStyles;
        document.head.appendChild(styleElement);

        // Cleanup khi component unmount
        return () => {
            document.head.removeChild(styleElement);
        };
    }, []);

    // Xử lý nội dung blog để hỗ trợ dark mode
    useEffect(() => {
        if (blog?.body) {
            // Xử lý nội dung code
            let content = blog.body;

            // Thêm class dark:bg-gray-800 cho các thẻ pre
            content = content.replace(/<pre([^>]*)>/g, '<pre$1 class="dark:bg-gray-800 dark:text-gray-200 relative">');

            // Thêm class dark:text-gray-200 cho các thẻ code không nằm trong pre
            content = content.replace(/<code(?![^>]*pre)([^>]*)>/g, '<code$1 class="dark:bg-gray-700 dark:text-pink-400">');

            // Thêm class dark:text-gray-200 cho các phần tử văn bản
            content = content.replace(/<p([^>]*)>/g, '<p$1 class="dark:text-gray-200">');
            content = content.replace(/<span([^>]*)>/g, '<span$1 class="dark:text-gray-200">');

            // Xử lý tables
            content = content.replace(/<table([^>]*)>/g, '<table$1 class="dark:border-gray-700">');
            content = content.replace(/<th([^>]*)>/g, '<th$1 class="dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700">');
            content = content.replace(/<td([^>]*)>/g, '<td$1 class="dark:border-gray-700 dark:text-gray-200">');

            // Xử lý text in đậm trong dark mode
            content = content.replace(/<strong([^>]*)>/g, '<strong$1 class="dark:text-blue-400">');
            content = content.replace(/<b([^>]*)>/g, '<b$1 class="dark:text-blue-400">');

            // Xử lý hình ảnh trong nội dung
            content = content.replace(/<img([^>]*)src="([^"]*)"([^>]*)>/g, (match, before, src, after) => {
                // Kiểm tra xem img đã có class chưa
                const hasClass = /class="([^"]*)"/.test(before) || /class="([^"]*)"/.test(after);

                if (hasClass) {
                    // Nếu đã có class, thêm vào class đó
                    return match.replace(/class="([^"]*)"/, 'class="$1 content-image"');
                } else {
                    // Nếu chưa có class, thêm mới
                    return `<img${before}src="${src}"${after} class="content-image">`;
                }
            });

            // Xử lý figure và figcaption
            content = content.replace(/<figure([^>]*)>/g, '<figure$1 class="my-8">');
            content = content.replace(/<figcaption([^>]*)>/g, '<figcaption$1 class="text-center text-sm text-gray-600 dark:text-gray-400 mt-2">');

            setProcessedContent(content);
        }
    }, [blog?.body]);

    // Áp dụng syntax highlighting và thêm nút copy
    useEffect(() => {
        if (blogContentRef.current) {
            const codeBlocks = blogContentRef.current.querySelectorAll('pre code');

            codeBlocks.forEach((codeBlock, index) => {
                // Lấy nội dung code
                const code = (codeBlock as HTMLElement).textContent || '';

                // Xác định ngôn ngữ
                const classes = (codeBlock as HTMLElement).className.split(' ');
                let language = '';
                for (const cls of classes) {
                    if (cls.startsWith('language-')) {
                        language = cls.replace('language-', '');
                        break;
                    }
                }

                // Tạo phiên bản highlight cho light mode
                const lightCodeEl = document.createElement('code');
                lightCodeEl.className = codeBlock.className + ' hljs';
                lightCodeEl.textContent = code;
                hljs.highlightElement(lightCodeEl);

                // Tạo phiên bản highlight cho dark mode
                const darkCodeEl = document.createElement('code');
                darkCodeEl.className = codeBlock.className + ' hljs-dark';
                darkCodeEl.textContent = code;
                hljs.highlightElement(darkCodeEl);

                // Thay thế code block gốc với cả hai phiên bản
                const parent = codeBlock.parentElement;
                if (parent) {
                    parent.innerHTML = '';
                    parent.appendChild(lightCodeEl);
                    parent.appendChild(darkCodeEl);

                    // Thêm label ngôn ngữ lập trình
                    if (language) {
                        const languageLabel = document.createElement('div');
                        languageLabel.className = 'code-language-label';
                        languageLabel.textContent = language;
                        parent.appendChild(languageLabel);
                    }

                    // Thêm nút copy code
                    const header = document.createElement('div');
                    header.className = 'code-block-header';

                    const copyButton = document.createElement('button');
                    copyButton.className = 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200 flex items-center justify-center w-8 h-8 rounded-md bg-white/90 dark:bg-gray-700/90 shadow-sm';
                    copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path></svg>';
                    copyButton.title = 'Sao chép code';

                    copyButton.addEventListener('click', () => {
                        navigator.clipboard.writeText(code).then(() => {
                            copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>';
                            copyButton.classList.add('text-green-500', 'dark:text-green-400');

                            setTimeout(() => {
                                copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path></svg>';
                                copyButton.classList.remove('text-green-500', 'dark:text-green-400');
                            }, 2000);
                        });
                    });

                    header.appendChild(copyButton);
                    parent.appendChild(header);
                }
            });
        }
    }, [processedContent]);

    // Xử lý khi có lỗi
    if (error) {
        return (
            <>
                <Head>
                    <title>{`${blog.title} | My Blog`}</title>
                    <meta name="description" content={blog.excerpt} />
                    <meta property="og:title" content={blog.title} />
                    <meta property="og:description" content={blog.excerpt} />
                    <meta property="og:image" content={blog.thumbnail_url || blog.featured_image || '/default-image.jpg'} />
                    <meta property="og:url" content={typeof window !== 'undefined' ? window.location.href : ''} />
                    <meta name="twitter:card" content="summary_large_image" />
                </Head>

                <div className="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
                    <Navbar />
                    <div className="pt-24 max-w-4xl mx-auto px-4 py-8">
                        <div className="text-center">
                            <h1 className="text-2xl font-bold text-red-500 dark:text-red-400 mb-4">Lỗi</h1>
                            <p className="text-gray-600 dark:text-gray-400 mb-8">{error}</p>
                            <Link
                                href="/blogs"
                                className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Quay lại danh sách bài viết
                            </Link>
                        </div>
                    </div>
                </div>
            </>
        );
    }

    // Hiển thị type của bài viết
    const displayType = typeMapping[blog.type] || blog.type;
    const typeColors = getTypeColor(displayType);

    // Xử lý chia sẻ bài viết
    const handleShare = () => {
        setShowShareModal(true);
        setLinkCopied(false);
    };

    const copyToClipboard = () => {
        navigator.clipboard.writeText(window.location.href);
        setLinkCopied(true);

        // Hiển thị thông báo đã copy
        setTimeout(() => {
            setLinkCopied(false);
        }, 2000);
    };

    const shareToFacebook = () => {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
    };

    const shareToTwitter = () => {
        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(blog.title)}`, '_blank');
    };

    const shareToLinkedIn = () => {
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}`, '_blank');
    };

    return (
        <div className="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
            <Navbar />
            <div className="pt-16">
                <div className="max-w-7xl mx-auto px-4 py-8">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5 }}
                    >
                        {/* Nút quay lại */}
                        <div className="mb-6">
                            <Link
                                href="/blogs"
                                className="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-apple-blue dark:hover:text-blue-400 transition-colors duration-300"
                            >
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Quay lại danh sách bài viết
                            </Link>
                        </div>

                        {/* Header bài viết */}
                        <div className="mb-8">
                            {/* Type badge */}
                            <div className="mb-4">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className={`rounded-full border ${typeColors.light} ${typeColors.dark} transition-colors duration-300`}
                                >
                                    {displayType}
                                </Button>
                            </div>

                            {/* Tiêu đề */}
                            <h1 className="text-4xl font-bold text-apple-dark-gray dark:text-white mb-4 transition-colors duration-500">
                                {blog.title}
                            </h1>

                            {/* Thông tin bài viết */}
                            <div className="flex flex-wrap items-center gap-4 text-gray-600 dark:text-gray-400 mb-6">
                                <div className="flex items-center gap-2">
                                    <Calendar className="h-4 w-4" />
                                    <span>{blog.date}</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Clock className="h-4 w-4" />
                                    <span>{blog.reading_time} phút đọc</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Eye className="h-4 w-4" />
                                    <span>{blog.views} lượt xem</span>
                                </div>
                                <div className="flex items-center gap-2 text-amber-500 dark:text-amber-400">
                                    <Star className="h-4 w-4 fill-current" />
                                    <span>{blog.stars}</span>
                                </div>
                            </div>

                            {/* Thông tin tác giả */}
                            <div className="flex items-center gap-3 mb-6">
                                <Avatar className="h-12 w-12">
                                    <img
                                        src={blog.author.avatar}
                                        alt={blog.author.name}
                                        onError={(e) => {
                                            (e.target as HTMLImageElement).src = 'https://github.com/shadcn.png';
                                        }}
                                    />
                                </Avatar>
                                <div>
                                    <div className="font-medium text-apple-dark-gray dark:text-white transition-colors duration-500">
                                        {blog.author.name}
                                    </div>
                                    {blog.author.bio && (
                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                            {blog.author.bio}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Ảnh bìa */}
                        {(blog.featured_image || blog.thumbnail_url) && (
                            <div className="mb-8 relative overflow-hidden group">
                                <div className="rounded-xl overflow-hidden shadow-lg border border-gray-100 dark:border-gray-800 transition-all duration-300 group-hover:shadow-xl">
                                    <div className="relative aspect-[21/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                                        <img
                                            src={blog.thumbnail_url}
                                            alt={blog.title}
                                            className="w-full h-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-105"
                                            loading="lazy"
                                            onError={(e) => {
                                                (e.target as HTMLImageElement).src = 'https://via.placeholder.com/1200x600?text=Blog+Image';
                                            }}
                                        />
                                        {/* Gradient overlay cho dark mode */}
                                        <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 dark:opacity-40 transition-opacity duration-300"></div>
                                    </div>

                                    {/* Caption nếu cần */}
                                    {blog.type && (
                                        <div className="absolute top-4 left-4">
                                            <span className={`inline-block px-3 py-1 rounded-full text-sm font-medium ${getTypeColor(displayType).light} ${getTypeColor(displayType).dark}`}>
                                                {displayType}
                                            </span>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Nội dung bài viết */}
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                            <div className="lg:col-span-9">
                                {/* Nội dung chính */}
                                <div className="prose dark:prose-dark prose-img:rounded-lg prose-headings:text-apple-dark-gray dark:prose-headings:text-white prose-a:text-apple-blue dark:prose-a:text-blue-400 prose-pre:bg-gray-100 dark:prose-pre:bg-gray-800 max-w-none">
                                    <div dangerouslySetInnerHTML={{ __html: processedContent }} ref={blogContentRef} />
                                </div>

                                {/* Tags và Categories */}
                                <div className="mt-8 border-t border-gray-200 dark:border-gray-800 pt-6">
                                    {/* Categories */}
                                    {blog.categories && blog.categories.length > 0 && (
                                        <div className="mb-4">
                                            <h3 className="text-lg font-semibold mb-3 text-apple-dark-gray dark:text-white">
                                                Danh mục
                                            </h3>
                                            <div className="flex flex-wrap gap-2">
                                                {blog.categories.map((category, i) => {
                                                    const colors = getTagColor(category);
                                                    return (
                                                        <Link
                                                            key={`category-${i}`}
                                                            href={`/blogs?category=${createSlug(category)}`}
                                                            className={`inline-flex items-center px-3 py-1.5 rounded-full text-sm ${colors.light} ${colors.dark} shadow-sm transition-all duration-300 hover:shadow-md hover:scale-105`}
                                                        >
                                                            {category}
                                                        </Link>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    )}

                                    {/* Tags */}
                                    {blog.tags && blog.tags.length > 0 && (
                                        <div>
                                            <h3 className="text-lg font-semibold mb-3 text-apple-dark-gray dark:text-white">
                                                Tags
                                            </h3>
                                            <div className="flex flex-wrap gap-2">
                                                {blog.tags.map((tag, i) => (
                                                    <span
                                                        key={`tag-${i}`}
                                                        className="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:shadow-sm transition-all duration-300 hover:scale-105"
                                                    >
                                                        <span className="text-blue-500 dark:text-blue-400 mr-1">#</span>
                                                        {tag}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* Share button */}
                                <div className="mt-6">
                                    <Button
                                        variant="outline"
                                        className="flex items-center gap-2 bg-white hover:bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors duration-300"
                                        onClick={handleShare}
                                    >
                                        <Share2 className="h-4 w-4" />
                                        Chia sẻ bài viết
                                    </Button>

                                    {/* Share modal */}
                                    {showShareModal && (
                                        <div className="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
                                            <div className="pointer-events-auto absolute max-w-md w-full" style={{ top: '5rem', right: '1rem' }}>
                                                <motion.div
                                                    initial={{ opacity: 0, y: -20, scale: 0.95 }}
                                                    animate={{ opacity: 1, y: 0, scale: 1 }}
                                                    className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700"
                                                >
                                                    <div className="flex items-center justify-between mb-4">
                                                        <h3 className="text-lg font-semibold text-apple-dark-gray dark:text-white">
                                                            Chia sẻ bài viết
                                                        </h3>
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            className="h-8 w-8 p-0 rounded-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                                            onClick={() => setShowShareModal(false)}
                                                        >
                                                            <span className="sr-only">Đóng</span>
                                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" className="h-4 w-4">
                                                                <path d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z" fill="currentColor" fillRule="evenodd" clipRule="evenodd"></path>
                                                            </svg>
                                                        </Button>
                                                    </div>

                                                    {/* URL bài viết */}
                                                    <div className="flex items-center gap-2 mb-6">
                                                        <div className="relative flex-1 group">
                                                            <input
                                                                type="text"
                                                                value={window.location.href}
                                                                readOnly
                                                                className="w-full p-2 pr-20 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent outline-none"
                                                            />
                                                            <Button
                                                                onClick={copyToClipboard}
                                                                className="absolute right-1 top-1 bottom-1 bg-apple-blue hover:bg-blue-600 text-white dark:bg-blue-600 dark:hover:bg-blue-700 px-3 h-auto min-h-0 rounded-md"
                                                            >
                                                                {linkCopied ? (
                                                                    <span className="flex items-center">
                                                                        <Check className="h-4 w-4 mr-1" /> Đã copy
                                                                    </span>
                                                                ) : (
                                                                    <span className="flex items-center">
                                                                        <Copy className="h-4 w-4 mr-1" /> Copy
                                                                    </span>
                                                                )}
                                                            </Button>
                                                        </div>
                                                    </div>

                                                    {/* Chia sẻ qua mạng xã hội */}
                                                    <div className="mb-6">
                                                        <h4 className="text-sm font-medium mb-3 text-gray-600 dark:text-gray-400">
                                                            Chia sẻ qua mạng xã hội
                                                        </h4>
                                                        <div className="flex items-center gap-3">
                                                            <Button
                                                                variant="outline"
                                                                className="flex-1 flex items-center justify-center gap-2 bg-white hover:bg-blue-50 text-blue-600 border-blue-200 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-900/50 dark:hover:bg-blue-900/20"
                                                                onClick={shareToFacebook}
                                                            >
                                                                <Facebook className="h-5 w-5" />
                                                                <span>Facebook</span>
                                                            </Button>
                                                            <Button
                                                                variant="outline"
                                                                className="flex-1 flex items-center justify-center gap-2 bg-white hover:bg-sky-50 text-sky-500 border-sky-200 dark:bg-gray-800 dark:text-sky-400 dark:border-sky-900/50 dark:hover:bg-sky-900/20"
                                                                onClick={shareToTwitter}
                                                            >
                                                                <Twitter className="h-5 w-5" />
                                                                <span>Twitter</span>
                                                            </Button>
                                                            <Button
                                                                variant="outline"
                                                                className="flex-1 flex items-center justify-center gap-2 bg-white hover:bg-blue-50 text-blue-700 border-blue-200 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-900/50 dark:hover:bg-blue-900/20"
                                                                onClick={shareToLinkedIn}
                                                            >
                                                                <Linkedin className="h-5 w-5" />
                                                                <span>LinkedIn</span>
                                                            </Button>
                                                        </div>
                                                    </div>
                                                </motion.div>
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* Điều hướng bài viết (trước/sau) */}
                                <div className="flex flex-wrap md:flex-nowrap justify-between gap-4 mt-12 pt-6 border-t border-gray-200 dark:border-gray-800">
                                    {navigation.previous ? (
                                        <Link
                                            href={`/blogs/${navigation.previous.slug}`}
                                            className="flex-1 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow duration-300"
                                        >
                                            <div className="flex items-center text-gray-600 dark:text-gray-400 mb-2">
                                                <ChevronLeft className="h-4 w-4 mr-1" />
                                                <span>Bài trước</span>
                                            </div>
                                            <h3 className="font-medium text-apple-dark-gray dark:text-white line-clamp-2">
                                                {navigation.previous.title}
                                            </h3>
                                        </Link>
                                    ) : (
                                        <div className="flex-1"></div>
                                    )}

                                    {navigation.next ? (
                                        <Link
                                            href={`/blogs/${navigation.next.slug}`}
                                            className="flex-1 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow duration-300 text-right"
                                        >
                                            <div className="flex items-center justify-end text-gray-600 dark:text-gray-400 mb-2">
                                                <span>Bài sau</span>
                                                <ChevronRight className="h-4 w-4 ml-1" />
                                            </div>
                                            <h3 className="font-medium text-apple-dark-gray dark:text-white line-clamp-2">
                                                {navigation.next.title}
                                            </h3>
                                        </Link>
                                    ) : (
                                        <div className="flex-1"></div>
                                    )}
                                </div>
                            </div>

                            {/* Sidebar */}
                            <div className="lg:col-span-3">
                                {/* Bài viết liên quan */}
                                {relatedBlogs && relatedBlogs.length > 0 && (
                                    <div className="mb-8">
                                        <h3 className="text-xl font-semibold mb-4 text-apple-dark-gray dark:text-white">
                                            Bài viết liên quan
                                        </h3>
                                        <div className="space-y-4">
                                            {relatedBlogs.map((post) => (
                                                <Link
                                                    key={post.id}
                                                    href={`/blogs/${post.slug}`}
                                                    className="block p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow duration-300"
                                                >
                                                    {post.thumbnail_url && (
                                                        <div className="mb-3 rounded-md overflow-hidden">
                                                            <img
                                                                src={post.thumbnail_url}
                                                                alt={post.title}
                                                                className="w-full h-32 object-cover"
                                                                loading="lazy"
                                                                onError={(e) => {
                                                                    (e.target as HTMLImageElement).src = 'https://via.placeholder.com/400x200?text=Blog+Image';
                                                                }}
                                                            />
                                                        </div>
                                                    )}
                                                    <h4 className="font-medium text-apple-dark-gray dark:text-white line-clamp-2 hover:text-apple-blue dark:hover:text-blue-400 transition-colors duration-300">
                                                        {post.title}
                                                    </h4>
                                                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                                        {post.date}
                                                    </p>
                                                </Link>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* Bài viết mới nhất */}
                                {latestBlogs && latestBlogs.length > 0 && (
                                    <div>
                                        <h3 className="text-xl font-semibold mb-4 text-apple-dark-gray dark:text-white">
                                            Bài viết mới nhất
                                        </h3>
                                        <div className="space-y-4">
                                            {latestBlogs.map((post) => (
                                                <Link
                                                    key={post.id}
                                                    href={`/blogs/${post.slug}`}
                                                    className="flex gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow duration-300"
                                                >
                                                    {post.thumbnail_url && (
                                                        <div className="w-16 h-16 rounded-md overflow-hidden flex-shrink-0">
                                                            <img
                                                                src={post.thumbnail_url}
                                                                alt={post.title}
                                                                className="w-full h-full object-cover"
                                                                loading="lazy"
                                                                onError={(e) => {
                                                                    (e.target as HTMLImageElement).src = 'https://via.placeholder.com/100?text=Blog';
                                                                }}
                                                            />
                                                        </div>
                                                    )}
                                                    <div className="flex-1">
                                                        <h4 className="font-medium text-apple-dark-gray dark:text-white line-clamp-2 text-sm hover:text-apple-blue dark:hover:text-blue-400 transition-colors duration-300">
                                                            {post.title}
                                                        </h4>
                                                        <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            {post.date}
                                                        </p>
                                                    </div>
                                                </Link>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </motion.div>
                </div>
            </div>
        </div>
    );
};

export default BlogDetail; 