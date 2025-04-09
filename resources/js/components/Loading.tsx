import React from 'react';
import { motion } from 'framer-motion';
import { cn } from '@/lib/utils';

const Loading = () => {
    return (
        <motion.div
            initial={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-gray-900"
        >
            <div className="relative">
                {/* Gradient circles */}
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="absolute w-32 h-32 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full animate-ping opacity-20" />
                    <div className="absolute w-24 h-24 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full animate-ping opacity-40 delay-75" />
                    <div className="absolute w-16 h-16 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full animate-ping opacity-60 delay-150" />
                </div>

                {/* Loading text */}
                <motion.div
                    initial={{ scale: 0.5, opacity: 0 }}
                    animate={{ scale: 1, opacity: 1 }}
                    transition={{ duration: 0.5 }}
                    className="relative z-10 text-center"
                >
                    <div className="flex items-center justify-center gap-4 mb-2">
                        <div className="text-4xl running-tiger">
                            🐯
                        </div>
                        <div className="text-4xl font-bold">
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-gradient">
                                Đang tải chờ xíu
                            </span>
                        </div>
                    </div>

                    {/* Loading bar */}
                    <div className="w-48 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <motion.div
                            initial={{ x: '-100%' }}
                            animate={{ x: '100%' }}
                            transition={{
                                duration: 1,
                                repeat: Infinity,
                                ease: 'linear'
                            }}
                            className="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"
                        />
                    </div>
                </motion.div>
            </div>
        </motion.div>
    );
};

export default Loading; 