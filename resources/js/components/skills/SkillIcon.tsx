
import React from 'react';
import { cn } from '@/lib/utils';
import { LucideIcon } from 'lucide-react';

interface SkillIconProps {
  Icon: LucideIcon;
  name: string;
  index: number;
  isInView: boolean;
}

const SkillIcon: React.FC<SkillIconProps> = ({ Icon, name, index, isInView }) => {
  return (
    <div 
      className={cn(
        "bg-[#22273a] rounded-lg p-4 flex items-center justify-center transition-all duration-500 hover:scale-110 hover:bg-apple-blue/10",
        "transform translate-x-[-100px] opacity-0"
      )}
      style={{
        animation: isInView 
          ? `slideInRight 0.8s forwards ${0.1 * (index + 1)}s` 
          : 'none',
        animationTimingFunction: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
      }}
    >
      <Icon 
        className="w-10 h-10 text-apple-blue group-hover:text-white" 
        strokeWidth={1.5} 
      />
    </div>
  );
};

export default SkillIcon;
