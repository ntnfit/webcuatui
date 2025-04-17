import React from 'react';
import { Link } from '@inertiajs/react';
import { Star, ArrowRight } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { motion } from 'framer-motion';
import { cn } from '@/lib/utils';

interface Article {
    id: number | string;
    title: string;
    excerpt: string;
    image?: string;
    thumbnail_url?: string;
    stars: number;
    tags: string[];
    slug?: string;
}

interface LatestArticlesProps {
    articles: Article[];
}
const LatestArticles: React.FC<LatestArticlesProps> = ({ articles }) => {
    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: 0.2
            }
        }
    };

    const itemVariants = {
        hidden: { y: 20, opacity: 0 },
        visible: {
            y: 0,
            opacity: 1,
            transition: {
                duration: 0.5
            }
        }
    };

    const titleVariants = {
        hidden: { scale: 0.8, opacity: 0 },
        visible: {
            scale: 1,
            opacity: 1,
            transition: {
                duration: 0.8,
                ease: "easeOut"
            }
        }
    };

    return (
        <section className="py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            {/* Background decoration */}
            <div className="absolute inset-0 -z-10">
                <div className="absolute inset-0 bg-gradient-radial from-blue-50/30 to-transparent dark:from-blue-900/10 dark:to-transparent opacity-70" />
            </div>

            <div className="max-w-7xl mx-auto">
                {/* Animated border container */}
                <div className="relative p-[1px] rounded-2xl overflow-hidden bg-white dark:bg-gray-900">
                    {/* Animated gradient border */}
                    <div className="absolute inset-0">
                        <div className="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-border-flow" />
                    </div>

                    {/* Content container with background */}
                    <div className="relative bg-white dark:bg-gray-900 rounded-2xl p-8">
                        <motion.div
                            className="text-center mb-16"
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true }}
                            variants={titleVariants}
                        >
                            <h2 className="text-4xl md:text-5xl font-bold text-apple-dark-gray dark:text-white">
                                Bài Viết Mới Nhất
                            </h2>
                            <div className="mt-4 w-24 h-1 bg-apple-blue mx-auto rounded-full" />
                        </motion.div>

                        <motion.div
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                            variants={containerVariants}
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true }}
                        >
                            {articles.map((article) => (
                                <motion.div
                                    key={article.id}
                                    variants={itemVariants}
                                    whileHover={{ y: -5, transition: { duration: 0.2 } }}
                                    className="group bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300"
                                >
                                    <Link
                                        href={`/blogs/${article.id}`}
                                        className="block relative h-48 overflow-hidden"
                                    >
                                        <div className="absolute inset-0 bg-gradient-to-b from-transparent to-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                                        <img
                                            src={article.image || article.thumbnail_url || '/images/placeholder.jpg'}
                                            alt={article.title}
                                            className="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
                                            onError={(e) => {
                                                const target = e.target as HTMLImageElement;
                                                target.onerror = null;
                                                target.src = '/images/placeholder.jpg';
                                            }}
                                        />
                                    </Link>
                                    <div className="p-6">
                                        <div className="flex items-center justify-between mb-4">
                                            <Link
                                                href={`/blogs/${article.id}`}
                                                className="text-xl font-semibold text-apple-dark-gray dark:text-white line-clamp-1 group-hover:text-apple-blue dark:group-hover:text-blue-400 transition-colors"
                                            >
                                                {article.title}
                                            </Link>
                                            <div className="flex items-center text-yellow-500">
                                                <Star className="h-4 w-4 fill-current" />
                                                <span className="ml-1">{article.stars}</span>
                                            </div>
                                        </div>
                                        <p className="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                            {article.excerpt}
                                        </p>
                                        <div className="flex flex-wrap gap-2 mb-4">
                                            {article.tags && article.tags.length > 0 ? article.tags.map((tag, index) => (
                                                <span
                                                    key={index}
                                                    className="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full transition-colors hover:bg-apple-blue hover:text-white"
                                                >
                                                    {tag}
                                                </span>
                                            )) : null}
                                        </div>

                                        <div className="pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <Link
                                                href={`/blogs/${article.id}`}
                                                className="inline-flex items-center text-sm font-medium text-apple-blue dark:text-blue-400 hover:text-apple-blue/80 dark:hover:text-blue-300 transition-colors"
                                            >
                                                Đọc thêm
                                                <ArrowRight className="ml-1 h-3.5 w-3.5" />
                                            </Link>
                                        </div>
                                    </div>
                                </motion.div>
                            ))}
                        </motion.div>

                        <motion.div
                            className="mt-12 text-center"
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            whileHover={{ scale: 1.05 }}
                        >
                            <Link
                                href="/blogs"
                                className="inline-flex items-center px-6 py-3 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500 dark:from-purple-500 dark:to-pink-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300"
                            >
                                Xem tất cả bài viết
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Link>
                        </motion.div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default LatestArticles; 