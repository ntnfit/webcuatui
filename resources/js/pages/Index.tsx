import React, { useEffect } from 'react';
import Hero from '../components/Hero';
import About from '../components/About';
import Skills from '../components/Skills';
import Projects from '../components/Projects';
import LatestArticles from '../components/LatestArticles';
import Contact from '../components/Contact';
import Footer from '../components/Footer';
import Navbar from '../components/Navbar';
import { useToast } from "../hooks/use-toast";

interface BlogPost {
    id: number;
    title: string;
    excerpt: string;
    image?: string;
    thumbnail_url?: string;
    stars: number;
    tags: string[];
}

interface IndexPageProps {
    latestArticles: BlogPost[];
}

const Index: React.FC<IndexPageProps> = ({ latestArticles }) => {
    const { toast } = useToast();

    const articlesWithImage = latestArticles.map(article => ({
        ...article,
        image: article.thumbnail_url || article.image || '/images/placeholder.jpg'
    }));

    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1 }
        );

        document.querySelectorAll('.section-fade-in').forEach((el) => observer.observe(el));

        document.querySelectorAll('.staggered-animation').forEach((container) => {
            const elements = container.querySelectorAll('*');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    (el as HTMLElement).style.opacity = '1';
                    (el as HTMLElement).style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
        });

        setTimeout(() => {
            toast({
                title: "Chào mừng bạn 👋",
                description: "Khám phá portfolio về cả Frontend và Backend của tôi",
            });
        }, 1500);

        return () => observer.disconnect();
    }, [toast]);

    return (
        <div className="relative overflow-x-hidden dark:bg-gray-900">
            <Navbar />
            <div className="pt-16">
                <Hero />
                <About />
                <Skills />
                <Projects />
                <LatestArticles articles={articlesWithImage} />
                <Contact />
                <Footer />
            </div>
        </div>
    );
};

export default Index;
