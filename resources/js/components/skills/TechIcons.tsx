import React, { useRef, useEffect, useState } from 'react';
import { motion, useAnimation } from 'framer-motion';
import { cn } from '@/lib/utils';

interface TechIcon {
  name: string;
  icon: string;
  color: string;
  darkColor?: string;
}

interface TechIconsProps {
  containerRef: React.RefObject<HTMLDivElement>;
  isInView: boolean;
}

const techIcons: TechIcon[] = [
  { name: 'React', icon: '⚛️', color: 'text-blue-500', darkColor: 'dark:text-blue-400' },
  { name: 'TypeScript', icon: '📘', color: 'text-blue-600', darkColor: 'dark:text-blue-500' },
  { name: 'Next.js', icon: '▲', color: 'text-black', darkColor: 'dark:text-white' },
  { name: 'Node.js', icon: '🟢', color: 'text-green-600', darkColor: 'dark:text-green-500' },
  { name: 'MongoDB', icon: '🍃', color: 'text-green-500', darkColor: 'dark:text-green-400' },
  { name: 'PostgreSQL', icon: '🐘', color: 'text-blue-700', darkColor: 'dark:text-blue-600' },
  { name: 'GraphQL', icon: '📊', color: 'text-pink-600', darkColor: 'dark:text-pink-500' },
  { name: 'Docker', icon: '🐳', color: 'text-blue-400', darkColor: 'dark:text-blue-300' },
  { name: 'AWS', icon: '☁️', color: 'text-orange-500', darkColor: 'dark:text-orange-400' },
  { name: 'Git', icon: '📦', color: 'text-orange-600', darkColor: 'dark:text-orange-500' },
  { name: 'Laravel', icon: '🔥', color: 'text-red-500', darkColor: 'dark:text-red-400' },
  { name: 'SAP B1', icon: '💼', color: 'text-blue-800', darkColor: 'dark:text-blue-300' },
];

const TechIcons: React.FC<TechIconsProps> = ({ containerRef, isInView }) => {
  const groupRef = useRef<HTMLDivElement>(null);
  const [direction, setDirection] = useState(1);
  const [speed, setSpeed] = useState(30);
  const [isPaused, setIsPaused] = useState(false);
  const [dragX, setDragX] = useState(0);
  const controls = useAnimation();
  const [isDarkMode, setIsDarkMode] = useState(false);

  useEffect(() => {
    const checkDarkMode = () => {
      setIsDarkMode(document.documentElement.classList.contains('dark'));
    };

    checkDarkMode();
    const observer = new MutationObserver(checkDarkMode);
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class']
    });

    return () => observer.disconnect();
  }, []);

  useEffect(() => {
    const animate = async () => {
      if (!isPaused) {
        await controls.start({
          x: direction === 1 ? '-100%' : '0%',
          transition: {
            duration: speed,
            ease: 'linear',
            repeat: Infinity,
            repeatType: 'loop',
            repeatDelay: 0
          }
        });
      }
    };

    animate();
  }, [direction, speed, controls, isPaused]);

  const handleDragStart = () => {
    setIsPaused(true);
    controls.stop();
  };

  const handleDrag = (_: any, info: { delta: { x: number } }) => {
    setDragX(prevDragX => prevDragX + info.delta.x);
    controls.set({ x: dragX });
  };

  const handleDragEnd = (_: any, info: { velocity: { x: number } }) => {
    setIsPaused(false);
    setDirection(info.velocity.x > 0 ? -1 : 1);
    controls.start({
      x: direction === 1 ? '-100%' : '0%',
      transition: {
        duration: speed,
        ease: 'linear',
        repeat: Infinity,
        repeatType: 'loop',
        repeatDelay: 0
      }
    });
  };

  const handleIconClick = (iconName: string) => {
    console.log(`${iconName} clicked`);
    // Could add a toast notification here
  };

  return (
    <div className="relative w-full overflow-hidden mt-12 rounded-xl bg-[#232839]/50 p-6">
      <div className="mb-4 text-xl text-center text-white font-semibold">
        <span className="text-apple-blue">Tech</span> Stack
      </div>

      <motion.div
        ref={groupRef}
        className="flex space-x-8 cursor-grab active:cursor-grabbing"
        animate={controls}
        initial={{ x: '0%' }}
        drag="x"
        dragConstraints={{ left: -1000, right: 1000 }}
        onDragStart={handleDragStart}
        onDrag={handleDrag}
        onDragEnd={handleDragEnd}
        dragElastic={0.1}
      >
        {/* Duplicate icons to create infinite scroll effect */}
        {[...techIcons, ...techIcons].map((icon, index) => (
          <motion.div
            key={`${icon.name}-${index}`}
            className={cn(
              "flex flex-col items-center justify-center space-y-2 px-2 py-3",
              "hover:bg-white/5 rounded-lg transition-all duration-300"
            )}
            whileHover={{ scale: 1.1, y: -5 }}
            whileTap={{ scale: 0.95 }}
            onClick={() => handleIconClick(icon.name)}
          >
            <span className="text-4xl">{icon.icon}</span>
            <span className={cn("text-xs font-medium", icon.color, icon.darkColor)}>
              {icon.name}
            </span>
          </motion.div>
        ))}
      </motion.div>

      {/* Gradient overlays for smooth edges */}
      <div className="absolute left-0 top-0 h-full w-16 bg-gradient-to-r from-[#1A1F2C] to-transparent z-10"></div>
      <div className="absolute right-0 top-0 h-full w-16 bg-gradient-to-l from-[#1A1F2C] to-transparent z-10"></div>
    </div>
  );
};

export default TechIcons;
