export interface BlogPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    featured_image: string;
    published_at: string;
    tags: string[];
    author: {
        name: string;
        avatar: string;
    };
} 