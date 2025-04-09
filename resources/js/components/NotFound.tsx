import React from 'react';
import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { motion } from 'framer-motion';
import { ArrowLeft } from 'lucide-react';

const NotFound: React.FC = () => {
    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
            <div className="max-w-md w-full text-center">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                >
                    <h1 className="text-9xl font-bold text-apple-blue dark:text-blue-400 mb-4">404</h1>
                    <h2 className="text-2xl font-semibold text-gray-800 dark:text-white mb-2">
                        Trang không tồn tại
                    </h2>
                    <p className="text-gray-600 dark:text-gray-400 mb-8">
                        Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.
                    </p>
                    <Link href="/">
                        <Button className="bg-apple-blue hover:bg-apple-blue/90 text-white">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Quay lại trang chủ
                        </Button>
                    </Link>
                </motion.div>
            </div>
        </div>
    );
};

export default NotFound; 