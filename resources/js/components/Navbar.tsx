import React, { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Menu, X, Moon, Sun } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { cn } from '@/lib/utils';
import { prefersDark, useAppearance } from '@/hooks/use-appearance';


const Navbar: React.FC = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [activeSection, setActiveSection] = useState('home');
  const { url } = usePage();

  // use custom hook for theme
  const { appearance, updateAppearance } = useAppearance();
  const isDark =
    appearance === 'dark' || (appearance === 'system' && prefersDark());

  useEffect(() => {
    // Scroll spy for active section
    const handleScroll = () => {
      const sections = ['home', 'about', 'projects', 'contact'];
      const scrollPosition = window.scrollY + 100;

      for (const section of sections) {
        const el = document.getElementById(section);
        if (!el) continue;
        const { offsetTop, offsetHeight } = el;
        if (
          scrollPosition >= offsetTop &&
          scrollPosition < offsetTop + offsetHeight
        ) {
          setActiveSection(section);
          break;
        }
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const toggleTheme = () => {
    // toggle between light and dark
    updateAppearance(isDark ? 'light' : 'dark');
  };

  const scrollToSection = async (
    e: React.MouseEvent<HTMLAnchorElement>,
    sectionId: string
  ) => {
    e.preventDefault();
    if (url.startsWith('/blogs')) {
      await router.visit(`/#${sectionId}`);
      return;
    }

    const el = document.getElementById(sectionId);
    if (!el) return;

    const headerOffset = 80;
    const pos = el.getBoundingClientRect().top + window.pageYOffset - headerOffset;
    window.scrollTo({ top: pos, behavior: 'smooth' });

    el.style.transition = 'outline 0.3s ease';
    el.style.outline = '2px solid rgba(59, 130, 246, 0.5)';
    el.style.outlineOffset = '4px';
    setTimeout(() => {
      el.style.outline = 'none';
    }, 1000);

    setIsOpen(false);
    setActiveSection(sectionId);
  };

  const menuItems = [
    { id: 'home', label: 'Trang chủ', href: '/#home' },
    { id: 'about', label: 'Về tôi', href: '/#about' },
    { id: 'projects', label: 'Dự án', href: '/#projects' },
    { id: 'contact', label: 'Liên hệ', href: '/#contact' },
    { id: 'blogs', label: 'Blog', href: '/blogs' },
  ];

  const isActive = (item: typeof menuItems[0]) => {
    if (item.id === 'blogs') return url.startsWith('/blogs');
    if (url.startsWith('/blogs')) return false;
    return activeSection === item.id;
  };

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 transition-colors duration-500">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex-shrink-0">
            <span className="text-2xl font-bold bg-gradient-to-r from-blue-600 via-purple-500 to-pink-500 text-transparent bg-clip-text transform hover:scale-105 transition-all duration-300">
              HarryDev
            </span>
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-8">
            {menuItems.map((item) =>
              item.id === 'blogs' ? (
                <Link
                  key={item.id}
                  href={item.href}
                  className={cn(
                    'text-sm font-medium transition-colors duration-300',
                    isActive(item)
                      ? 'text-blue-600 dark:text-blue-400 font-semibold'
                      : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400'
                  )}
                >
                  {item.label}
                </Link>
              ) : (
                <a
                  key={item.id}
                  href={item.href}
                  onClick={(e) => scrollToSection(e, item.id)}
                  className={cn(
                    'text-sm font-medium transition-colors duration-300',
                    isActive(item)
                      ? 'text-blue-600 dark:text-blue-400 font-semibold'
                      : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400'
                  )}
                >
                  {item.label}
                </a>
              )
            )}
            <div className="flex items-center space-x-2">
              <Sun className={cn(
                'h-4 w-4 transition-all duration-300',
                isDark
                  ? 'text-gray-400 opacity-50 scale-90'
                  : 'text-amber-500 opacity-100 scale-100'
              )} />
              <Switch checked={isDark} onCheckedChange={toggleTheme} className={cn(
                'data-[state=checked]:bg-blue-600 data-[state=unchecked]:bg-amber-300',
                'transition-all duration-300'
              )} />
              <Moon className={cn(
                'h-4 w-4 transition-all duration-300',
                isDark
                  ? 'text-blue-400 opacity-100 scale-100'
                  : 'text-gray-400 opacity-50 scale-90'
              )} />
            </div>
          </div>

          {/* Mobile Menu Button */}
          <div className="md:hidden flex items-center">
            <div className="flex items-center space-x-2 mr-4">
              <Sun className={cn(
                'h-4 w-4 transition-all duration-300',
                isDark
                  ? 'text-gray-400 opacity-50 scale-90'
                  : 'text-amber-500 opacity-100 scale-100'
              )} />
              <Switch checked={isDark} onCheckedChange={toggleTheme} className={cn(
                'data-[state=checked]:bg-blue-600 dark:data-[state=checked]:bg-blue-500',
                'transition-all duration-300'
              )} />
              <Moon className={cn(
                'h-4 w-4 transition-all duration-300',
                isDark
                  ? 'text-blue-400 opacity-100 scale-100'
                  : 'text-gray-400 opacity-50 scale-90'
              )} />
            </div>
            <Button
              variant="ghost"
              size="icon"
              onClick={() => setIsOpen(!isOpen)}
              className="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300"
            >
              {isOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
            </Button>
          </div>
        </div>
      </div>

      {/* Mobile Menu */}
      <motion.div
        initial={false}
        animate={isOpen ? 'open' : 'closed'}
        variants={{
          open: { opacity: 1, height: 'auto' },
          closed: { opacity: 0, height: 0 },
        }}
        className="md:hidden bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 transition-colors duration-500"
      >
        <div className="px-4 py-2 space-y-1">
          {menuItems.map((item) =>
            item.id === 'blogs' ? (
              <Link
                key={item.id}
                href={item.href}
                onClick={() => setIsOpen(false)}
                className={cn(
                  'block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300',
                  isActive(item)
                    ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-gray-800 font-semibold'
                    : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800'
                )}
              >
                {item.label}
              </Link>
            ) : (
              <a
                key={item.id}
                href={item.href}
                onClick={(e) => scrollToSection(e, item.id)}
                className={cn(
                  'block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300',
                  isActive(item)
                    ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-gray-800 font-semibold'
                    : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800'
                )}
              >
                {item.label}
              </a>
            )
          )}
        </div>
      </motion.div>
    </nav>
  );
};

export default Navbar;
