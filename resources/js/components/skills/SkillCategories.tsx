
import React from 'react';

type SkillCategory = {
  title: string;
  technologies: string;
};

interface SkillCategoriesProps {
  categories: SkillCategory[];
  isInView: boolean;
}

const SkillCategories: React.FC<SkillCategoriesProps> = ({ categories, isInView }) => {
  return (
    <div className="space-y-8">
      {categories.map((category, index) => (
        <div 
          key={index}
          className="skill-category"
          style={{ 
            animationDelay: `${0.2 + index * 0.1}s`,
            opacity: isInView ? 1 : 0
          }}
        >
          <div className="flex items-center">
            <div className="text-white mr-2">•</div>
            <div className="skill-category-title text-white text-xl">{category.title}</div>
          </div>
          <div className="ml-5 skill-technologies text-lg mt-1">
            {category.technologies}
          </div>
        </div>
      ))}
    </div>
  );
};

export default SkillCategories;
