
import React from 'react';

export const BoxesIcon = (props: React.SVGProps<SVGSVGElement>) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    {...props}
  >
    <path d="M3 7v4h4V7H3z" />
    <path d="M10 7v4h4V7h-4z" />
    <path d="M17 7v4h4V7h-4z" />
    <path d="M3 14v4h4v-4H3z" />
    <path d="M10 14v4h4v-4h-4z" />
    <path d="M17 14v4h4v-4h-4z" />
  </svg>
);

export const PuzzleIcon = (props: React.SVGProps<SVGSVGElement>) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    {...props}
  >
    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
    <path d="M12 8v8" />
    <path d="M8 12h8" />
  </svg>
);
