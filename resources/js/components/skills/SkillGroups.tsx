import React from 'react';
import { motion } from 'framer-motion';
import {
  Code2, Server, Database, BoxesIcon, Layers, PuzzleIcon
} from 'lucide-react';
import { cn } from '../../lib/utils';

interface SkillGroupsProps {
  isInView: boolean;
}

const skillGroups = [
  {
    title: "Frontend",
    skills: "ReactJS, VueJS, TailwindCSS, UI/UX",
    Icon: Code2,
    iconColor: "text-blue-600 dark:text-blue-400"
  },
  {
    title: "Backend",
    skills: "Laravel, REST API, OAuth2",
    Icon: Server,
    iconColor: "text-purple-600 dark:text-purple-400"
  },
  {
    title: "Database",
    skills: "SQL Server, MySQL, SAP HANA",
    Icon: Database,
    iconColor: "text-green-500 dark:text-green-400"
  },
  {
    title: "ERP Integration",
    skills: "SAP B1 SDK (DI API, Service Layer), Workflow tư vấn",
    Icon: BoxesIcon,
    iconColor: "text-pink-500 dark:text-pink-400"
  }
];

const SkillGroups: React.FC<SkillGroupsProps> = ({ isInView }) => {
  return (
    <div className="space-y-8">
      {skillGroups.map((group, index) => (
        <motion.div
          key={group.title}
          initial={{ opacity: 0, x: -50 }}
          animate={isInView ? { opacity: 1, x: 0 } : {}}
          transition={{ duration: 0.5, delay: 0.2 + index * 0.1 }}
          className="group relative p-[1px] rounded-2xl overflow-hidden bg-white/5 dark:bg-gray-900/5 transition-colors duration-500"
        >
          {/* Animated gradient border */}
          <div className="absolute inset-0">
            <div className="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-border-flow" />
          </div>

          {/* Content container */}
          <div className="relative bg-white/5 dark:bg-gray-900/5 backdrop-blur-sm rounded-2xl p-6 hover:bg-white/10 dark:hover:bg-gray-800/50 transition-all duration-300">
            <div className="flex items-center mb-3">
              <group.Icon
                className={cn(
                  "mr-3 transition-colors duration-300",
                  "text-gray-600 dark:text-gray-400",
                  group.iconColor,
                  "group-hover:text-blue-500 dark:group-hover:text-blue-400"
                )}
                size={24}
              />
              <h3 className={cn(
                "text-xl font-semibold transition-colors duration-300",
                "text-gray-800 dark:text-gray-200",
                "group-hover:text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"
              )}>
                {group.title}
              </h3>
            </div>
            <p className={cn(
              "ml-9 transition-colors duration-300",
              "text-gray-600 dark:text-gray-400",
              "group-hover:text-gray-800 dark:group-hover:text-gray-200"
            )}>
              {group.skills}
            </p>
          </div>
        </motion.div>
      ))}
    </div>
  );
};

export default SkillGroups;
