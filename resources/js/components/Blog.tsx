import React, { useState } from 'react';
import { BlogPost } from '@/types';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ToggleGroup, ToggleGroupItem } from "@/components/ui/toggle-group";
import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useIsMobile } from '@/hooks/use-mobile';
import { CalendarDays, User, ArrowRight } from 'lucide-react';

const posts: BlogPost[] = [
    {
        id: 1,
        title: "Getting Started with React and TypeScript",
        slug: "getting-started-with-react-and-typescript",
        excerpt: "Learn how to set up a new React project with TypeScript and best practices for type safety.",
        content: "Lorem ipsum dolor sit amet, consectetur adipiscing elit...",
        featured_image: "/images/blog/react-typescript.jpg",
        published_at: "2024-03-15",
        tags: ["React", "TypeScript", "Frontend"],
        author: {
            name: "John Doe",
            avatar: "/images/avatars/john-doe.jpg"
        }
    },
    {
        id: 2,
        title: "Building Scalable APIs with Laravel",
        slug: "building-scalable-apis-with-laravel",
        excerpt: "Discover techniques for building robust and scalable APIs using Laravel framework.",
        content: "Lorem ipsum dolor sit amet, consectetur adipiscing elit...",
        featured_image: "/images/blog/laravel-api.jpg",
        published_at: "2024-03-10",
        tags: ["Laravel", "API", "Backend"],
        author: {
            name: "Jane Smith",
            avatar: "/images/avatars/jane-smith.jpg"
        }
    },
    {
        id: 3,
        title: "Modern CSS Techniques",
        slug: "modern-css-techniques",
        excerpt: "Explore modern CSS features and techniques for creating beautiful user interfaces.",
        content: "Lorem ipsum dolor sit amet, consectetur adipiscing elit...",
        featured_image: "/images/blog/modern-css.jpg",
        published_at: "2024-03-05",
        tags: ["CSS", "Frontend", "Design"],
        author: {
            name: "Mike Johnson",
            avatar: "/images/avatars/mike-johnson.jpg"
        }
    }
];

const Blog: React.FC = () => {
    const [selectedTag, setSelectedTag] = useState<string | null>(null);
    const isMobile = useIsMobile();

    const filteredPosts = selectedTag
        ? posts.filter(post => post.tags.includes(selectedTag))
        : posts;

    const allTags = Array.from(new Set(posts.flatMap(post => post.tags)));

    return (
        <section className="py-16 px-4 md:px-8">
            <div className="max-w-7xl mx-auto">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                    className="text-center mb-12"
                >
                    <h2 className="text-3xl md:text-4xl font-bold mb-4">Bài viết mới nhất</h2>
                    <p className="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Khám phá những bài viết mới nhất về phát triển web, thiết kế và công nghệ.
                    </p>
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.2 }}
                    className="mb-8"
                >
                    <ToggleGroup
                        type="single"
                        value={selectedTag || ""}
                        onValueChange={(value) => setSelectedTag(value || null)}
                        className="flex flex-wrap justify-center gap-2"
                    >
                        <ToggleGroupItem
                            value=""
                            className="data-[state=on]:bg-apple-blue data-[state=on]:text-white"
                        >
                            Tất cả
                        </ToggleGroupItem>
                        {allTags.map((tag: string, index: number) => (
                            <ToggleGroupItem
                                key={index}
                                value={tag}
                                className="data-[state=on]:bg-apple-blue data-[state=on]:text-white"
                            >
                                {tag}
                            </ToggleGroupItem>
                        ))}
                    </ToggleGroup>
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.4 }}
                    className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    {filteredPosts.map((post) => (
                        <motion.div
                            key={post.id}
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.5 }}
                        >
                            <Card className="hover-scale glass-card overflow-hidden h-full border border-gray-200 dark:border-gray-800">
                                {post.featured_image && (
                                    <div className="h-48 overflow-hidden">
                                        <img
                                            src={post.featured_image}
                                            alt={post.title}
                                            className="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                                        />
                                    </div>
                                )}
                                <CardHeader>
                                    <CardTitle className="text-xl">{post.title}</CardTitle>
                                    <CardDescription>{post.excerpt}</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex flex-wrap gap-2">
                                        {post.tags.map((tag: string, index: number) => (
                                            <span
                                                key={index}
                                                className="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded-full text-sm"
                                            >
                                                {tag}
                                            </span>
                                        ))}
                                    </div>
                                </CardContent>
                                <CardFooter className="flex justify-between items-center">
                                    <div className="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div className="flex items-center gap-1">
                                            <CalendarDays size={16} />
                                            <span>{post.published_at}</span>
                                        </div>
                                        <div className="flex items-center gap-1">
                                            <User size={16} />
                                            <span>{post.author.name}</span>
                                        </div>
                                    </div>
                                    <Link
                                        href={`/blogs/${post.slug}`}
                                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-transparent bg-apple-blue text-white dark:text-white hover:bg-apple-blue/90 transition-all duration-200 shadow-sm hover:shadow-md dark:hover:shadow-lg group"
                                    >
                                        <span className="group-hover:translate-x-1 transition-transform">Đọc thêm</span>
                                        <ArrowRight className="h-4 w-4" />
                                    </Link>
                                </CardFooter>
                            </Card>
                        </motion.div>
                    ))}
                </motion.div>

                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.5 }}
                    className="text-center mt-12"
                >
                    <Link
                        href="/blogs"
                        className="text-apple-blue hover:text-apple-blue/80 transition-colors"
                    >
                        Xem tất cả bài viết
                        <ArrowRight className="ml-2 h-4 w-4 inline" />
                    </Link>
                </motion.div>
            </div>
        </section>
    );
};

export default Blog; 