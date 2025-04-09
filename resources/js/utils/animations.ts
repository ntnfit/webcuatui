
import { useEffect, useState, useRef, RefObject } from 'react';

export function useInView<T extends HTMLElement>(
  options: IntersectionObserverInit = { threshold: 0.1 }
): [RefObject<T>, boolean] {
  const ref = useRef<T>(null);
  const [isInView, setIsInView] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        setIsInView(true);
        ref.current && observer.unobserve(ref.current);
      }
    }, options);

    if (ref.current) {
      observer.observe(ref.current);
    }

    return () => {
      observer.disconnect();
    };
  }, [options]);

  return [ref, isInView];
}

export function useScrollProgress(): number {
  const [scrollProgress, setScrollProgress] = useState(0);

  useEffect(() => {
    const handleScroll = () => {
      const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
      const progress = window.scrollY / totalHeight;
      setScrollProgress(progress);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return scrollProgress;
}

export function useAnimationDelay(baseDelay: number = 0.1, count: number = 1): string[] {
  return Array.from({ length: count }, (_, i) => `${baseDelay * (i + 1)}s`);
}
