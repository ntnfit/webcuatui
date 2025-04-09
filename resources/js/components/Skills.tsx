import React, { useRef } from 'react';
import { useInView } from '@/utils/animations';
import SkillsHeader from './skills/SkillsHeader';
import TechIcons from './skills/TechIcons';
import SkillGroups from './skills/SkillGroups';
import { motion } from 'framer-motion';

const Skills: React.FC = () => {
  const [ref, isInView] = useInView<HTMLDivElement>();
  const containerRef = useRef<HTMLDivElement>(null);

  return (
    <section
      id="skills"
      ref={ref}
      className="py-24 px-6 bg-[#1A1F2C] dark:bg-gray-900 text-white relative overflow-hidden transition-colors duration-500"
    >
      <div className="absolute inset-0 bg-gradient-to-br from-[#1A1F2C] via-[#252A3A] to-[#1A1F2C] dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 z-0 transition-colors duration-500"></div>

      <div className="max-w-7xl mx-auto relative z-10">
        <SkillsHeader />

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={isInView ? { opacity: 1, y: 0 } : {}}
          transition={{ duration: 0.6, delay: 0.3 }}
          className="grid grid-cols-1 md:grid-cols-2 gap-10 items-start"
          ref={containerRef}
        >
          {/* Left column - Skill groups */}
          <SkillGroups isInView={isInView} />

          {/* Right column - Tech icons with animations */}
          <TechIcons containerRef={containerRef} isInView={isInView} />
        </motion.div>
      </div>

      {/* Background light effects */}
      <div className="absolute top-1/3 -left-24 w-72 h-72 bg-blue-500/10 dark:bg-blue-600/10 rounded-full filter blur-3xl transition-colors duration-500"></div>
      <div className="absolute bottom-1/4 -right-24 w-80 h-80 bg-purple-500/10 dark:bg-purple-600/10 rounded-full filter blur-3xl transition-colors duration-500"></div>
    </section>
  );
};

export default Skills;
