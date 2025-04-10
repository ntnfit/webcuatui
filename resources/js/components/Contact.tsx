import React, { FormEvent, useState } from 'react';
import { cn } from '@/lib/utils';
import { useInView } from '@/utils/animations';
import { ContactInfo, ContactFormData } from '@/types';
import axios from 'axios';

const contactInfo: ContactInfo = {
  email: 'ntnguyen0310@gmail.com',
  phone: '0981710031',
  social: {
    github: 'https://github.com',
    linkedin: 'https://linkedin.com',
    twitter: 'https://twitter.com'
  }
};

const Contact: React.FC = () => {
  const [ref, isInView] = useInView<HTMLDivElement>();
  const [formData, setFormData] = useState<ContactFormData>({
    name: '',
    email: '',
    message: '',
    company_name: '',
    phone_number: ''
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setError(null);

    try {
      // Gửi dữ liệu đến Laravel backend
      const response = await axios.post('/api/contact', formData);

      console.log('Form submitted:', response.data);
      setIsSubmitting(false);
      setIsSubmitted(true);
      setFormData({
        name: '',
        email: '',
        message: '',
        company_name: '',
        phone_number: ''
      });

      // Reset success message after 5 seconds
      setTimeout(() => {
        setIsSubmitted(false);
      }, 5000);
    } catch (err) {
      setIsSubmitting(false);
      setError('Đã có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.');
      console.error('Error submitting form:', err);
    }
  };

  return (
    <section id="contact" ref={ref} className="py-24 px-6 bg-apple-gray/30 dark:bg-gray-900/30 transition-colors duration-500">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-16">
          <div className="inline-block px-3 py-1 rounded-full bg-apple-blue/10 dark:bg-blue-900/20 text-apple-blue dark:text-blue-400 text-xs font-medium mb-4 transition-colors duration-500">
            Liên Hệ Với Tôi
          </div>
          <h2 className="text-3xl md:text-4xl font-bold mb-4 tracking-tight text-apple-dark-gray dark:text-white transition-colors duration-500">
            Hãy Bắt Đầu Trò Chuyện
          </h2>
          <p className="text-lg text-apple-dark-gray/80 dark:text-gray-300/80 max-w-2xl mx-auto transition-colors duration-500">
            Cho dù bạn có một dự án trong tâm trí hay chỉ muốn kết nối, tôi luôn sẵn sàng thảo luận về những cơ hội mới.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-5 gap-12">
          <div
            className={cn(
              "lg:col-span-2 transition-all duration-500",
              isInView
                ? "opacity-100 transform translate-x-0"
                : "opacity-0 transform -translate-x-10"
            )}
          >
            <div className="bg-apple-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl h-full glass-card transition-colors duration-500">
              <h3 className="text-2xl font-bold mb-6 text-apple-dark-gray dark:text-white transition-colors duration-500">Thông Tin Liên Hệ</h3>

              <div className="space-y-6">
                <div className="flex items-start">
                  <div className="bg-apple-blue/10 dark:bg-blue-900/20 p-3 rounded-full mr-4 transition-colors duration-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M22 12H16L14 15H10L8 12H2" stroke="#0071E3" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      <path d="M5.45 5.11L2 12V18C2 18.5304 2.21071 19.0391 2.58579 19.4142C2.96086 19.7893 3.46957 20 4 20H20C20.5304 20 21.0391 19.7893 21.4142 19.4142C21.7893 19.0391 22 18.5304 22 18V12L18.55 5.11C18.3844 4.77679 18.1292 4.49637 17.813 4.30028C17.4967 4.10419 17.1321 4.0002 16.76 4H7.24C6.86792 4.0002 6.50326 4.10419 6.18704 4.30028C5.87083 4.49637 5.61558 4.77679 5.45 5.11Z" stroke="#0071E3" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  </div>
                  <div>
                    <h4 className="text-sm font-medium text-apple-dark-gray/60 dark:text-gray-400 mb-1 transition-colors duration-500">Email</h4>
                    <a href={`mailto:${contactInfo.email}`} className="text-apple-dark-gray dark:text-gray-300 hover:text-apple-blue dark:hover:text-blue-400 transition-colors duration-300">
                      {contactInfo.email}
                    </a>
                  </div>
                </div>

                <div className="flex items-start">
                  <div className="bg-apple-blue/10 dark:bg-blue-900/20 p-3 rounded-full mr-4 transition-colors duration-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8329 20.7293C21.7217 20.9845 21.5593 21.2136 21.3582 21.4019C21.1571 21.5901 20.9215 21.7335 20.6655 21.8227C20.4094 21.9119 20.1382 21.9451 19.87 21.92C16.7428 21.5856 13.7869 20.5341 11.19 18.85C8.77383 17.3147 6.72534 15.2662 5.19 12.85C3.49998 10.2412 2.44824 7.27097 2.12 4.13C2.09501 3.86347 2.12788 3.59353 2.2165 3.33939C2.30513 3.08525 2.44757 2.85177 2.63477 2.65218C2.82196 2.45258 3.04981 2.29202 3.30379 2.18214C3.55778 2.07226 3.83234 2.01635 4.11 2.02H7.11C7.59531 2.01544 8.06579 2.1649 8.43376 2.4453C8.80173 2.72569 9.04208 3.11956 9.11 3.56C9.23668 4.63016 9.47151 5.68262 9.81 6.69C9.9445 7.0357 9.97366 7.4156 9.8939 7.78117C9.81415 8.14673 9.62886 8.48093 9.36 8.74L8.09 10.01C9.51356 12.4135 11.5865 14.4864 13.99 15.91L15.26 14.64C15.5191 14.3711 15.8533 14.1858 16.2188 14.1061C16.5844 14.0263 16.9643 14.0555 17.31 14.19C18.3174 14.5285 19.3698 14.7633 20.44 14.89C20.8869 14.9585 21.2855 15.2032 21.5656 15.5775C21.8457 15.9518 21.9916 16.4296 22 16.92Z" stroke="#0071E3" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  </div>
                  <div>
                    <h4 className="text-sm font-medium text-apple-dark-gray/60 dark:text-gray-400 mb-1 transition-colors duration-500">Điện thoại</h4>
                    <a href={`tel:${contactInfo.phone}`} className="text-apple-dark-gray dark:text-gray-300 hover:text-apple-blue dark:hover:text-blue-400 transition-colors duration-300">
                      {contactInfo.phone}
                    </a>
                  </div>
                </div>

                <div>
                  <h4 className="text-sm font-medium text-apple-dark-gray/60 dark:text-gray-400 mb-3 transition-colors duration-500">Mạng xã hội</h4>
                  <div className="flex space-x-4">
                    {contactInfo.social.github && (
                      <a
                        href={contactInfo.social.github}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="bg-apple-gray dark:bg-gray-700 p-3 rounded-full text-apple-dark-gray dark:text-gray-300 hover:bg-apple-blue dark:hover:bg-blue-600 hover:text-white transition-all duration-300"
                        aria-label="GitHub"
                      >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M9 19C4 20.5 4 16.5 2 16M16 22V18.13C16.0375 17.6532 15.9731 17.1738 15.811 16.7238C15.6489 16.2738 15.3929 15.8634 15.06 15.52C18.2 15.17 21.5 13.98 21.5 8.52C21.4997 7.12383 20.9627 5.7812 20 4.77C20.4559 3.54851 20.4236 2.19835 19.91 0.999999C19.91 0.999999 18.73 0.649999 16 2.48C13.708 1.85882 11.292 1.85882 9 2.48C6.27 0.649999 5.09 0.999999 5.09 0.999999C4.57638 2.19835 4.54414 3.54851 5 4.77C4.03013 5.7887 3.49252 7.14346 3.5 8.55C3.5 13.97 6.8 15.16 9.94 15.55C9.611 15.89 9.35726 16.2954 9.19531 16.7399C9.03335 17.1844 8.96681 17.6581 9 18.13V22" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                      </a>
                    )}

                    {contactInfo.social.linkedin && (
                      <a
                        href={contactInfo.social.linkedin}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="bg-apple-gray dark:bg-gray-700 p-3 rounded-full text-apple-dark-gray dark:text-gray-300 hover:bg-apple-blue dark:hover:bg-blue-600 hover:text-white transition-all duration-300"
                        aria-label="LinkedIn"
                      >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M16 8C17.5913 8 19.1174 8.63214 20.2426 9.75736C21.3679 10.8826 22 12.4087 22 14V21H18V14C18 13.4696 17.7893 12.9609 17.4142 12.5858C17.0391 12.2107 16.5304 12 16 12C15.4696 12 14.9609 12.2107 14.5858 12.5858C14.2107 12.9609 14 13.4696 14 14V21H10V14C10 12.4087 10.6321 10.8826 11.7574 9.75736C12.8826 8.63214 14.4087 8 16 8Z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                          <path d="M6 9H2V21H6V9Z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                          <path d="M4 6C5.10457 6 6 5.10457 6 4C6 2.89543 5.10457 2 4 2C2.89543 2 2 2.89543 2 4C2 5.10457 2.89543 6 4 6Z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                      </a>
                    )}

                    {contactInfo.social.twitter && (
                      <a
                        href={contactInfo.social.twitter}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="bg-apple-gray dark:bg-gray-700 p-3 rounded-full text-apple-dark-gray dark:text-gray-300 hover:bg-apple-blue dark:hover:bg-blue-600 hover:text-white transition-all duration-300"
                        aria-label="Twitter"
                      >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M22 4C22 4 21.3 6.1 20 7.4C21.6 17.4 10.6 24.7 2 19C4.2 19.1 6.4 18.4 8 17C3 15.5 0.5 9.6 3 5C5.2 7.6 8.6 9.1 12 9C11.1 4.8 16 2.4 19 5.2C20.1 5.2 22 4 22 4Z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                      </a>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            className={cn(
              "lg:col-span-3 transition-all duration-500",
              isInView
                ? "opacity-100 transform translate-x-0"
                : "opacity-0 transform translate-x-10"
            )}
          >
            <div className="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl h-full glass-card transition-colors duration-500">
              <h3 className="text-2xl font-bold mb-6 text-apple-dark-gray dark:text-white transition-colors duration-500">Gửi Tin Nhắn</h3>

              {isSubmitted ? (
                <div className="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl p-6 text-center transition-colors duration-500">
                  <svg className="w-12 h-12 mx-auto mb-4 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <h4 className="text-xl font-bold mb-2">Tin Nhắn Đã Được Gửi!</h4>
                  <p>Cảm ơn bạn đã liên hệ. Tôi sẽ phản hồi trong thời gian sớm nhất.</p>
                </div>
              ) : (
                <form onSubmit={handleSubmit}>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label htmlFor="name" className="block text-sm font-medium text-apple-dark-gray/70 dark:text-gray-300 mb-1 transition-colors duration-500">
                        Họ tên
                      </label>
                      <input
                        type="text"
                        id="name"
                        name="name"
                        value={formData.name}
                        onChange={handleChange}
                        required
                        className="w-full px-4 py-3 rounded-xl border border-apple-gray dark:border-gray-700 bg-white dark:bg-gray-900 text-apple-dark-gray dark:text-white focus:border-apple-blue dark:focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-apple-blue/30 dark:focus:ring-blue-500/30 transition-all duration-300"
                        placeholder="Họ và tên của bạn"
                      />
                    </div>

                    <div>
                      <label htmlFor="email" className="block text-sm font-medium text-apple-dark-gray/70 dark:text-gray-300 mb-1 transition-colors duration-500">
                        Email
                      </label>
                      <input
                        type="email"
                        id="email"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
                        required
                        className="w-full px-4 py-3 rounded-xl border border-apple-gray dark:border-gray-700 bg-white dark:bg-gray-900 text-apple-dark-gray dark:text-white focus:border-apple-blue dark:focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-apple-blue/30 dark:focus:ring-blue-500/30 transition-all duration-300"
                        placeholder="Địa chỉ email của bạn"
                      />
                    </div>

                    <div>
                      <label htmlFor="phone_number" className="block text-sm font-medium text-apple-dark-gray/70 dark:text-gray-300 mb-1 transition-colors duration-500">
                        Số điện thoại
                      </label>
                      <input
                        type="tel"
                        id="phone_number"
                        name="phone_number"
                        value={formData.phone_number}
                        onChange={handleChange}
                        className="w-full px-4 py-3 rounded-xl border border-apple-gray dark:border-gray-700 bg-white dark:bg-gray-900 text-apple-dark-gray dark:text-white focus:border-apple-blue dark:focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-apple-blue/30 dark:focus:ring-blue-500/30 transition-all duration-300"
                        placeholder="Số điện thoại của bạn"
                      />
                    </div>

                    <div>
                      <label htmlFor="company_name" className="block text-sm font-medium text-apple-dark-gray/70 dark:text-gray-300 mb-1 transition-colors duration-500">
                        Công ty
                      </label>
                      <input
                        type="text"
                        id="company_name"
                        name="company_name"
                        value={formData.company_name}
                        onChange={handleChange}
                        className="w-full px-4 py-3 rounded-xl border border-apple-gray dark:border-gray-700 bg-white dark:bg-gray-900 text-apple-dark-gray dark:text-white focus:border-apple-blue dark:focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-apple-blue/30 dark:focus:ring-blue-500/30 transition-all duration-300"
                        placeholder="Tên công ty của bạn (nếu có)"
                      />
                    </div>

                    <div className="md:col-span-2">
                      <label htmlFor="message" className="block text-sm font-medium text-apple-dark-gray/70 dark:text-gray-300 mb-1 transition-colors duration-500">
                        Tin nhắn
                      </label>
                      <textarea
                        id="message"
                        name="message"
                        value={formData.message}
                        onChange={handleChange}
                        required
                        rows={5}
                        className="w-full px-4 py-3 rounded-xl border border-apple-gray dark:border-gray-700 bg-white dark:bg-gray-900 text-apple-dark-gray dark:text-white focus:border-apple-blue dark:focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-apple-blue/30 dark:focus:ring-blue-500/30 transition-all duration-300 resize-none"
                        placeholder="Tôi có thể giúp gì cho bạn?"
                      />
                    </div>

                    {error && (
                      <div className="md:col-span-2 text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-200 dark:border-red-800">
                        {error}
                      </div>
                    )}

                    <div className="md:col-span-2">
                      <button
                        type="submit"
                        disabled={isSubmitting}
                        className={cn(
                          "w-fit px-8 py-3 rounded-xl font-medium transition-all duration-300",
                          "text-white disabled:opacity-70 disabled:cursor-not-allowed",
                          "bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-500", // 🌞 Light mode: gradient xanh dương
                          "dark:bg-gradient-to-r dark:from-purple-600 dark:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-purple-600", // 🌚 Dark mode: gradient tím
                          "shadow-lg hover:shadow-xl",
                          "transform hover:-translate-y-0.5",
                          "focus:outline-none focus:ring-2 focus:ring-blue-500/50 dark:focus:ring-purple-500/50",
                          "mx-auto block"
                        )}
                      >
                        {isSubmitting ? (
                          <span className="flex items-center justify-center gap-2">
                            <svg className="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang gửi...
                          </span>
                        ) : (
                          'Gửi Tin Nhắn'
                        )}
                      </button>
                    </div>
                  </div>
                </form>
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Contact;
