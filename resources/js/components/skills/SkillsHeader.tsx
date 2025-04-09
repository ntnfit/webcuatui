import React from 'react';
import { motion } from 'framer-motion';

const SkillsHeader: React.FC = () => {
  return (
    <motion.div
      className="text-center mb-16"
      initial={{ opacity: 0, y: -20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.6 }}
    >
      <div className="inline-block px-3 py-1 rounded-full bg-apple-blue/10 dark:bg-blue-900/20 text-apple-blue dark:text-blue-400 text-xs font-medium mb-4 transition-colors duration-500">
        • Tech Stack
      </div>

      <h2 className="text-4xl md:text-5xl font-bold mb-8 tracking-tight text-white dark:text-gray-100 transition-colors duration-500">
        My Technical Expertise
      </h2>

      <p className="text-lg text-gray-300 dark:text-gray-400 max-w-2xl mx-auto transition-colors duration-500">
        I specialize in creating responsive web applications and ERP integrations
        using modern frameworks and technologies.
      </p>
    </motion.div>
  );
};

export default SkillsHeader;
