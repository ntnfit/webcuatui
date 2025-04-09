
import React from 'react';

const BackgroundElements: React.FC = () => {
  return (
    <>
      <div className="absolute top-8 right-8 w-24 h-24 border border-gray-700/30 rounded-full"></div>
      <div className="absolute bottom-16 left-16 w-40 h-40 border border-gray-700/30 rounded-full"></div>
      <div className="absolute top-1/2 left-1/4 w-4 h-4 bg-green-500/20 rounded-full"></div>
      <div className="absolute top-1/3 right-1/4 w-6 h-6 bg-blue-500/10 rounded-full"></div>
      <div className="absolute bottom-1/4 right-1/3 w-3 h-3 bg-violet-500/20 rounded-full"></div>
    </>
  );
};

export default BackgroundElements;
