import React from 'react';
import { motion } from 'framer-motion';
import { cn } from '@/lib/utils';
import { useInView } from '@/utils/animations';

interface Company {
  id: string;
  name: string;
  logo: string;
  link: string;
  description: string;
}

const companies: Company[] = [
  {
    id: '1',
    name: 'San Hà Foods',
    logo: 'https://sanha.vn/wp-content/uploads/2024/07/logo.png',
    link: 'https://sanha.vn/',
    description: 'Rise up with quality · Safe food supply chain from farm to table · SanHàFoodstore · Foodie Buddy · A+ Gourmet Food · SanHà HORECA.'
  },
  {
    id: '2',
    name: 'Hồng Ký',
    logo: 'https://www.hongky.com/wp-content/uploads/2024/08/hing.png',
    link: 'https://www.hongky.com/',
    description: 'Công ty Hồng Ký sản xuất và phân phối các sản phẩm về máy móc cơ khí'
  },
  {
    id: '3',
    name: 'E-block',
    logo: 'https://eblock.com.vn/wp-content/uploads/2024/07/logo-header.png',
    link: 'https://eblock.com.vn',
    description: 'Autoclaved aerated concrete is a modern construction material, including AAC EBLOCK bricks and AAC EPANEL panels, creating sustainable, energy-saving buildings.'
  },
  {
    id: '4',
    name: 'Tazmo',
    logo: 'https://tazmo-vn.com/wp-content/uploads/2021/12/logo_TAZMO_2021.png',
    link: 'https://tazmo-vn.com',
    description: 'TAZMO Việt Nam sản xuất thiết bị tự động hóa (FA) và các loại máy móc công nghiệp.'
  },
  {
    id: '5',
    name: "Aeondelight",
    logo: 'https://aeondelight-vietnam.com.vn/wp-content/uploads/2024/02/logo-6.svg',
    link: 'https://aeondelight-vietnam.com.vn',
    description: 'Over 12 years of offering Facility Management Services, AEON Delight Vietnam has been most esteemed in this sector.'
  },
  {
    id: '6',
    name: 'Cjolivenetworks',
    logo: 'https://en.cjolivenetworks.co.kr/images/common/logo-white.svg',
    link: 'https://en.cjolivenetworks.co.kr',
    description: 'CJ OliveNetworks, a lifestyle innovation company that leads the change in space and daily life based on digital technology and data.'
  },
  {
    id: '7',
    name: 'Việt Hưng Sài Gòn',
    logo: 'https://cdn.nhansu.vn/uploads/images/0B91D74C/logo/2018-12/fc4f6b551f484115966604ba4e6adffb_logo-Viet-Hung.png',
    link: 'http://viethung.com.vn/',
    description: 'Công ty bao bì Việt Hưng Sài Gòn'
  },
  {
    id: '8',
    name: 'betagen',
    logo: 'https://www.betagen.co.th/images/logo.png',
    link: 'https://www.betagen.co.th/',
    description: 'Betagen is a leading manufacturer of high-quality pharmaceutical products in Thailand.'
  },
  {
    id: '9',
    name: 'woodsland',
    logo: 'https://woodsland.vn/wp-content/uploads/2024/07/logo.svg',
    link: 'https://woodsland.vn',
    description: 'Woodsland is a leading manufacturer of high-quality pharmaceutical products in Vietnam.'
  },
  {
    id: '10',
    name: 'Nissey',
    logo: 'https://www.nihon-s.co.jp/wp-content/uploads/elementor/thumbs/logo001-poowskldjo65xxcjfqkcvjrcq33ci0zr2933ho3v3g.png',
    link: 'https://www.nihon-s.co.jp/group-company/nissey-vietnam/',
    description: 'Nissey is a leading manufacturer of high-quality pharmaceutical products in Vietnam.'
  },
  {
    id: '11',
    name: 'Nam Dung',
    logo: 'https://lh6.googleusercontent.com/proxy/pbw0HOscYPaji1hh6BrJjC7o_XHWLKAl-jkswJzk0gSQSKWjJjr8XNT7gkS1NGVzzCFaNSIbxKlJTvbY-95syIKODB3KwoEhAOZqwQ',
    link: 'http://www.namdung.vn',
    description: 'Nam Dung is a leading manufacturer of high-quality pharmaceutical products in Vietnam.'
  },
  {
    id: '12',
    name: 'usm',
    logo: 'https://usm.com.vn/wp-content/themes/usm/images/logo.svg',
    link: ' https://usm.com.vn/',
    description: 'usm is a leading manufacturer of high-quality pharmaceutical products in Vietnam.'
  }

];

const CompanyCard: React.FC<{ company: Company; index: number }> = ({ company, index }) => {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.5, delay: index * 0.1 }}
    >
      <motion.div
        whileHover={{ scale: 1.05 }}
        whileTap={{ scale: 0.95 }}
        className="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 hover:shadow-xl dark:hover:shadow-gray-700/30 transition-all duration-300"
      >
        <div className="flex flex-col items-center text-center space-y-4">
          {/* Logo Container */}
          <div className="relative w-24 h-24 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-xl p-2">
            <motion.div
              className="absolute inset-0 bg-blue-500/10 dark:bg-blue-400/10 rounded-xl"
              animate={{
                scale: [1, 1.1, 1],
                opacity: [0.5, 0.8, 0.5]
              }}
              transition={{
                duration: 3,
                repeat: Infinity,
                ease: "easeInOut"
              }}
            />
            <img
              src={company.logo}
              alt={company.name}
              className="w-16 h-16 object-contain relative z-10 transition-transform duration-300 group-hover:scale-110"
            />
          </div>

          {/* Company Name */}
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white transition-colors duration-300">
            {company.name}
          </h3>

          {/* Description */}
          <p className="text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">
            {company.description}
          </p>

          {/* View More Link */}
          <motion.a
            href={company.link}
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            className="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-300"
          >
            View Project
            <svg
              className="w-4 h-4 ml-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M9 5l7 7-7 7"
              />
            </svg>
          </motion.a>
        </div>

        {/* Hover Effect Border */}
        <div className="absolute inset-0 border-2 border-transparent group-hover:border-blue-500/50 dark:group-hover:border-blue-400/50 rounded-2xl transition-colors duration-300" />
      </motion.div>
    </motion.div>
  );
};

const Projects: React.FC = () => {
  const [ref, isInView] = useInView<HTMLDivElement>();

  return (
    <section id="projects" className="py-24 bg-gray-50 dark:bg-gray-900 transition-colors duration-500">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Phần mô tả */}
        <div className="text-center mb-16">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-block px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-medium mb-4 transition-colors duration-500"
          >
            Dự án đã triển khai
          </motion.div>
          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="text-3xl md:text-4xl font-bold mb-4 tracking-tight text-gray-900 dark:text-white transition-colors duration-500"
          >
            Đối tác & Khách hàng
          </motion.h2>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto transition-colors duration-500"
          >
            Tôi đã có cơ hội làm việc và phát triển các giải pháp công nghệ cho nhiều đối tác trong và ngoài nước. Dưới đây là một số khách hàng tiêu biểu mà tôi đã có cơ hội hợp tác.
          </motion.p>
        </div>

        {/* Grid logo */}
        <div className="relative">
          {/* Gradient overlays */}
          <div className="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-gray-50 dark:from-gray-900 to-transparent z-10" />
          <div className="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-gray-50 dark:from-gray-900 to-transparent z-10" />

          {/* Logo grid */}
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 md:gap-12 items-center overflow-hidden">
            {companies.map((company, index) => (
              <motion.a
                key={company.id}
                href={company.link}
                target="_blank"
                rel="noopener noreferrer"
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className={cn(
                  "flex items-center justify-center group",
                  index % 3 === 0 ? "col-span-2" : "col-span-1"
                )}
              >
                <motion.div
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                  className="relative w-full h-12 md:h-16"
                >
                  <img
                    src={company.logo}
                    alt={company.name}
                    className="w-full h-full object-contain grayscale group-hover:grayscale-0 opacity-60 group-hover:opacity-100 transition duration-300"
                  />
                </motion.div>
              </motion.a>
            ))}
          </div>
        </div>

        {/* View more button */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ delay: 0.3 }}
          className="text-center mt-16"
        >
          <motion.a
            href="#contact"
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            className="font-semibold inline-flex items-center px-6 py-3 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500 dark:from-purple-500 dark:to-pink-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300"
          >
            <span>Liên hệ hợp tác</span>
            <svg
              className="w-5 h-5 ml-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M17 8l4 4m0 0l-4 4m4-4H3"
              />
            </svg>
          </motion.a>
        </motion.div>
      </div>
    </section>
  );
};

export default Projects;
