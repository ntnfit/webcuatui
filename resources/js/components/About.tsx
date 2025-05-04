import React from 'react';
import { cn } from '@/lib/utils';
import { useInView } from '@/utils/animations';
import { Check } from 'lucide-react';
import meImage from '../image/me.jpg';

const About: React.FC = () => {
    const [ref, isInView] = useInView<HTMLDivElement>();

    return (
        <section
            id="about"
            ref={ref}
            className="py-16 px-6 sm:px-10 lg:px-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-500"
        >
            <div className="max-w-6xl mx-auto">
                <h2
                    className={cn(
                        'text-3xl md:text-4xl font-bold text-center mb-8 tracking-tight transition-all duration-500 font-sans',
                        isInView
                            ? 'text-gray-800 dark:text-white opacity-100 translate-y-0'
                            : 'opacity-0 translate-y-8'
                    )}
                >
                    ✨ Về Tôi
                </h2>

                <div
                    className={cn(
                        'grid grid-cols-1 md:grid-cols-2 gap-10 items-center transition-all duration-500',
                        isInView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'
                    )}
                >
                    {/* Thông tin giới thiệu */}
                    <div className="space-y-6">
                        <p className="text-base md:text-lg text-gray-700 dark:text-gray-300 leading-relaxed font-sans transition-colors duration-500">
                            Xin chào! Mình là <strong className="text-sky-600 dark:text-sky-400 transition-colors duration-500">Harry Dev</strong>,
                            một lập trình viên đam mê với hơn 5 năm kinh nghiệm xây dựng ứng dụng web, hệ thống doanh nghiệp và tích hợp giải pháp công nghệ.
                            Mình luôn hướng đến việc tạo ra sản phẩm đẹp, nhanh, tối ưu và dễ sử dụng.
                        </p>

                        <ul className="space-y-3">
                            {[
                                'Phát triển WebApp với React, Next.js và Tailwind CSS',
                                'Triển khai & tối ưu hệ thống ERP cho doanh nghiệp',
                                'Tối ưu hóa hiệu suất và trải nghiệm người dùng (UX/UI)',
                                'Thiết kế API và tích hợp hệ thống SAP B1 / OData / SDK',
                            ].map((item, index) => (
                                <li key={index} className="flex items-start">
                                    <Check className="h-5 w-5 mt-1 text-sky-500 dark:text-blue-400 mr-3 flex-shrink-0 transition-colors duration-500" />
                                    <span className="text-gray-700 dark:text-gray-300 font-sans transition-colors duration-500">{item}</span>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Hình ảnh minh họa */}
                    <div className="relative group">
                        <div className="rounded-2xl overflow-hidden shadow-lg dark:shadow-gray-800/30 transform transition-all duration-500 group-hover:scale-105 group-hover:shadow-2xl">
                            <img
                                src={meImage}
                                alt="Harry Dev"
                                className="object-cover w-full h-full transition-all duration-500 ease-in-out group-hover:opacity-95"
                            />
                        </div>
                        <div className="absolute -inset-1 rounded-2xl bg-gradient-to-tr from-blue-300 via-pink-200 to-purple-300 dark:from-blue-900/30 dark:via-pink-900/20 dark:to-purple-900/30 opacity-20 blur-2xl z-[-1] transition-colors duration-500" />
                    </div>
                </div>
            </div>
        </section>
    );
};

export default About;
