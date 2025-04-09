import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';

const ProgressLoading: React.FC = () => {
    const [progress, setProgress] = useState(0);
    const [isVisible, setIsVisible] = useState(true);

    useEffect(() => {
        // Simulate loading progress
        const interval = setInterval(() => {
            setProgress((prevProgress) => {
                if (prevProgress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        setIsVisible(false);
                    }, 500);
                    return 100;
                }
                return prevProgress + 1;
            });
        }, 20);

        return () => clearInterval(interval);
    }, []);

    if (!isVisible) return null;

    return (
        <div className="fixed top-0 left-0 w-full h-1 z-50">
            <motion.div
                className="h-full bg-apple-blue dark:bg-blue-500"
                initial={{ width: 0 }}
                animate={{ width: `${progress}%` }}
                transition={{ duration: 0.2, ease: "easeInOut" }}
            />
            <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-sm font-medium text-apple-blue dark:text-blue-500">
                {progress}%
            </div>
        </div>
    );
};

export default ProgressLoading; 