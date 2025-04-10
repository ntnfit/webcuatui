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

export interface ContactInfo {
    email: string;
    phone: string;
    social: {
        github?: string;
        linkedin?: string;
        twitter?: string;
    };
}

export interface ContactFormData {
    name: string;
    email: string;
    message: string;
    company?: string;
    phone_number?: string;
    contact_reason_id?: number;
} 