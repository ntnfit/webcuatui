
import React from 'react';

const AnimationStyles: React.FC = () => {
  return (
    <style>
      {`
        @keyframes floatHorizontal {
          0% { transform: translateX(0px); }
          50% { transform: translateX(10px); }
          100% { transform: translateX(0px); }
        }

        @keyframes glow {
          0% { box-shadow: 0 0 5px rgba(255, 255, 255, 0.5); }
          50% { box-shadow: 0 0 20px rgba(255, 255, 255, 0.8); }
          100% { box-shadow: 0 0 5px rgba(255, 255, 255, 0.5); }
        }
        
        @keyframes pulse {
          0% { transform: scale(1); }
          50% { transform: scale(1.05); }
          100% { transform: scale(1); }
        }

        @keyframes slideInRight {
          from {
            transform: translateX(-100px);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }

        @keyframes fadeIn {
          from {
            opacity: 0;
          }
          to {
            opacity: 1;
          }
        }

        .skill-category {
          animation: fadeIn 0.6s ease-out forwards;
        }

        .tech-icon {
          transition: transform 0.3s ease-out;
        }
        
        .tech-icon:hover {
          transform: scale(1.1);
          animation: glow 1.5s infinite;
        }
        
        .floating {
          animation: floatHorizontal 3s ease-in-out infinite;
        }

        .skill-category-title {
          font-weight: bold;
        }

        .skill-technologies {
          color: #8899a6;
        }
      `}
    </style>
  );
};

export default AnimationStyles;
