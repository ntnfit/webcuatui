/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.ts",
        "./resources/**/*.tsx",
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                'apple-blue': '#0071e3',
                'apple-dark-gray': '#1d1d1f',
                'apple-gray': '#86868b',
                'apple-light-gray': '#f5f5f7',
                'apple-white': '#fbfbfd',
            },
            typography(theme) {
                return {
                    DEFAULT: {
                        css: {
                            color: theme('colors.gray.800'),
                            a: { color: theme('colors.apple-blue'), textDecoration: 'none', '&:hover': { textDecoration: 'underline' } },
                            strong: { color: theme('colors.apple-dark-gray') },
                            'ul > li::marker': { color: theme('colors.apple-blue') },
                            blockquote: {
                                borderLeftColor: theme('colors.apple-blue'),
                                color: theme('colors.gray.600'),
                            },
                            hr: { borderColor: theme('colors.gray.200') },
                            th: { backgroundColor: theme('colors.gray.100'), color: theme('colors.gray.700') },
                            td: { borderColor: theme('colors.gray.200') },
                            code: {
                                color: theme('colors.pink.600'),
                                backgroundColor: theme('colors.gray.100'),
                                padding: '0.2em 0.4em',
                                borderRadius: '0.25rem',
                                fontWeight: '500',
                            },
                            'pre code': {
                                backgroundColor: 'transparent',
                                padding: '0',
                                fontWeight: 'normal',
                                color: 'inherit',
                            },
                            pre: {
                                backgroundColor: theme('colors.gray.100'),
                                color: theme('colors.gray.800'),
                                borderRadius: '0.5rem',
                                padding: theme('spacing.4'),
                                boxShadow: 'inset 0 0 0 1px ' + theme('colors.gray.200'),
                                overflowX: 'auto',
                                position: 'relative',
                                marginBottom: '1.5em',
                            },
                        },
                    },
                    dark: {
                        css: {
                            color: theme('colors.gray.200'),
                            a: { color: theme('colors.blue.400') },
                            strong: { color: theme('colors.blue.400') },
                            'h1, h2, h3, h4': { color: theme('colors.white') },
                            'ul > li::marker': { color: theme('colors.blue.400') },
                            blockquote: {
                                color: theme('colors.gray.300'),
                                borderLeftColor: theme('colors.blue.500'),
                            },
                            hr: { borderColor: theme('colors.gray.700') },
                            th: { backgroundColor: theme('colors.gray.800'), color: theme('colors.gray.200') },
                            td: { borderColor: theme('colors.gray.700') },
                            code: {
                                color: theme('colors.pink.400'),
                                backgroundColor: theme('colors.gray.800'),
                            },
                            'pre code': {
                                backgroundColor: 'transparent',
                            },
                            pre: {
                                backgroundColor: theme('colors.gray.800'),
                                color: theme('colors.gray.200'),
                                boxShadow: 'inset 0 0 0 1px ' + theme('colors.gray.700'),
                                position: 'relative',
                            },
                            p: { color: theme('colors.gray.200') },
                            li: { color: theme('colors.gray.200') },
                            img: { backgroundColor: theme('colors.gray.800') },
                            'figure figcaption': { color: theme('colors.gray.400') },
                        },
                    },
                };
            },
        },
    },
    plugins: [require('@tailwindcss/typography')],
}