
import React from 'react';
import { LucideIcon } from 'lucide-react';
import SkillIcon from './SkillIcon';

interface SkillsGridProps {
  skillIcons: Array<{ Icon: LucideIcon, name: string }>;
  isInView: boolean;
}

const SkillsGrid: React.FC<SkillsGridProps> = ({ skillIcons, isInView }) => {
  return (
    <div className="grid grid-cols-3 sm:grid-cols-6 gap-6 justify-center items-center">
      {skillIcons.map((skill, index) => (
        <SkillIcon 
          key={skill.name}
          Icon={skill.Icon}
          name={skill.name}
          index={index}
          isInView={isInView}
        />
      ))}
    </div>
  );
};

export default SkillsGrid;
