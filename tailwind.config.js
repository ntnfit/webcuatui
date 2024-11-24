import preset from './vendor/filament/support/tailwind.config.preset'
const colors = require('tailwindcss/colors')
const { fontFamily } = require('tailwindcss/defaultTheme')

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.amber,
                success: colors.green,
                warning: colors.amber,
                cream: '#FFF9F5',
                butter: '#FDAE4B',
                'burnt-butter': '#f2911b',
                midnight: '#0F033A',
                evening: '#251A4D',
                merino: '#F2E7DD',
                hurricane: '#807575',
                dolphin: '#6C6489',
                'burnt-dolphin': '#454059',
                'peach-orange': '#FFC497',
                'seashell-peach': '#FFF0E8',
                'dawn-pink': '#F1E5E4',
                salmon: '#F89377',
                'fair-pink': '#FFEAE4',
            },
            fontFamily: {
                cursive: ['Kalam', ...fontFamily.serif],
                heading: ['Lexend', ...fontFamily.sans],
                mono: ['JetBrains Mono', ...fontFamily.sans],
                sans: ['DM Sans', ...fontFamily.sans],
                vietnam: ['Be Vietnam Pro', ...fontFamily.sans],
                'roboto-mono': ['Roboto Mono', ...fontFamily.sans],
            },
            maxWidth: {
                '8xl': '88rem',
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        a: {
                            '&:hover': {
                                color: theme('colors.primary.600'),
                            },
                            '&:focus': {
                                color: theme('colors.primary.600'),
                            },
                        },
                        blockquote: {
                            fontStyle: 'normal',
                        },
                        'blockquote p:first-of-type::before': {
                            content: 'none',
                        },
                        'blockquote p:first-of-type::after': {
                            content: 'none',
                        },
                        code: {
                            fontWeight: theme('fontWeight.normal'),
                        },
                    },
                },
                invert: {
                    css: {
                        a: {
                            '&:hover': {
                                color: theme('colors.primary.500'),
                            },
                            '&:focus': {
                                color: theme('colors.primary.500'),
                            },
                        },
                    },
                },
            }),
            zIndex: {
                '-1': '-1',
            },
            animation: {
                'border-move': 'border-move 3s linear infinite', // Keep it linear for seamless transitions
            }, keyframes: {
                'border-move': {
                    '0%': {
                        'border-top-color': 'rgba(0, 255, 0, 1)',
                        'border-right-color': 'rgba(0, 0, 0, 0)',
                        'border-bottom-color': 'rgba(0, 0, 0, 0)',
                        'border-left-color': 'rgba(0, 0, 0, 0)',
                    },
                    '25%': {
                        'border-top-color': 'rgba(0, 0, 0, 0)',
                        'border-right-color': 'rgba(0, 255, 0, 1)',
                        'border-bottom-color': 'rgba(0, 0, 0, 0)',
                        'border-left-color': 'rgba(0, 0, 0, 0)',
                    },
                    '50%': {
                        'border-top-color': 'rgba(0, 0, 0, 0)',
                        'border-right-color': 'rgba(0, 0, 0, 0)',
                        'border-bottom-color': 'rgba(0, 255, 0, 1)',
                        'border-left-color': 'rgba(0, 0, 0, 0)',
                    },
                    '75%': {
                        'border-top-color': 'rgba(0, 0, 0, 0)',
                        'border-right-color': 'rgba(0, 0, 0, 0)',
                        'border-bottom-color': 'rgba(0, 0, 0, 0)',
                        'border-left-color': 'rgba(0, 255, 0, 1)',
                    },
                    '100%': {
                        'border-top-color': 'rgba(0, 255, 0, 1)',
                        'border-right-color': 'rgba(0, 0, 0, 0)',
                        'border-bottom-color': 'rgba(0, 0, 0, 0)',
                        'border-left-color': 'rgba(0, 0, 0, 0)',
                    },
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
