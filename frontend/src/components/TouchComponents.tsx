'use client';

import { ReactNode, ButtonHTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

interface TouchButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost';
  size?: 'sm' | 'md' | 'lg' | 'xl';
  children: ReactNode;
  loading?: boolean;
  fullWidth?: boolean;
}

const TouchButton = ({
  variant = 'primary',
  size = 'md',
  children,
  loading = false,
  fullWidth = false,
  className,
  disabled,
  ...props
}: TouchButtonProps) => {
  const baseClasses = [
    'inline-flex items-center justify-center',
    'rounded-lg font-medium transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-offset-2',
    'active:scale-95 touch-manipulation',
    'disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none',
    // Touch-friendly minimum size
    'min-h-[44px] min-w-[44px]'
  ];

  const variants = {
    primary: [
      'bg-blue-600 text-white shadow-sm',
      'hover:bg-blue-700 focus:ring-blue-500',
      'active:bg-blue-800'
    ],
    secondary: [
      'bg-gray-100 text-gray-900 shadow-sm',
      'hover:bg-gray-200 focus:ring-gray-500',
      'active:bg-gray-300',
      'dark:bg-gray-700 dark:text-gray-100',
      'dark:hover:bg-gray-600 dark:active:bg-gray-800'
    ],
    danger: [
      'bg-red-600 text-white shadow-sm',
      'hover:bg-red-700 focus:ring-red-500',
      'active:bg-red-800'
    ],
    ghost: [
      'text-gray-700 hover:bg-gray-100',
      'focus:ring-gray-500 active:bg-gray-200',
      'dark:text-gray-300 dark:hover:bg-gray-700',
      'dark:active:bg-gray-600'
    ]
  };

  const sizes = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-2.5 text-sm',
    lg: 'px-6 py-3 text-base',
    xl: 'px-8 py-4 text-lg'
  };

  const widthClass = fullWidth ? 'w-full' : '';

  return (
    <button
      className={cn(
        baseClasses,
        variants[variant],
        sizes[size],
        widthClass,
        className
      )}
      disabled={disabled || loading}
      {...props}
    >
      {loading && (
        <svg
          className="animate-spin -ml-1 mr-2 h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            className="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            strokeWidth="4"
          />
          <path
            className="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          />
        </svg>
      )}
      {children}
    </button>
  );
};

interface TouchCardProps {
  children: ReactNode;
  className?: string;
  clickable?: boolean;
  onClick?: () => void;
  hover?: boolean;
}

const TouchCard = ({
  children,
  className,
  clickable = false,
  onClick,
  hover = true
}: TouchCardProps) => {
  const baseClasses = [
    'bg-white dark:bg-gray-800',
    'rounded-lg border border-gray-200 dark:border-gray-700',
    'shadow-sm'
  ];

  const interactiveClasses = clickable ? [
    'cursor-pointer transition-all duration-200',
    'active:scale-[0.98] touch-manipulation',
    hover ? 'hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600' : ''
  ] : [];

  const Component = clickable ? 'button' : 'div';

  return (
    <Component
      className={cn(baseClasses, interactiveClasses, className)}
      onClick={onClick}
    >
      {children}
    </Component>
  );
};

interface TouchInputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  helper?: string;
  leftIcon?: ReactNode;
  rightIcon?: ReactNode;
}

const TouchInput = ({
  label,
  error,
  helper,
  leftIcon,
  rightIcon,
  className,
  ...props
}: TouchInputProps) => {
  return (
    <div className="space-y-1">
      {label && (
        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
          {label}
        </label>
      )}
      <div className="relative">
        {leftIcon && (
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            {leftIcon}
          </div>
        )}
        <input
          className={cn(
            'block w-full rounded-lg border border-gray-300 dark:border-gray-600',
            'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100',
            'px-3 py-3 text-base', // Larger touch target
            'focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20',
            'disabled:bg-gray-50 disabled:text-gray-500',
            'placeholder:text-gray-400 dark:placeholder:text-gray-500',
            leftIcon ? 'pl-10' : '',
            rightIcon ? 'pr-10' : '',
            error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '',
            className
          )}
          {...props}
        />
        {rightIcon && (
          <div className="absolute inset-y-0 right-0 pr-3 flex items-center">
            {rightIcon}
          </div>
        )}
      </div>
      {error && (
        <p className="text-sm text-red-600 dark:text-red-400">{error}</p>
      )}
      {helper && !error && (
        <p className="text-sm text-gray-500 dark:text-gray-400">{helper}</p>
      )}
    </div>
  );
};

interface SwipeableCardProps {
  children: ReactNode;
  onSwipeLeft?: () => void;
  onSwipeRight?: () => void;
  className?: string;
}

const SwipeableCard = ({
  children,
  onSwipeLeft,
  onSwipeRight,
  className
}: SwipeableCardProps) => {
  let startX = 0;
  let startY = 0;

  const handleTouchStart = (e: React.TouchEvent) => {
    startX = e.touches[0].clientX;
    startY = e.touches[0].clientY;
  };

  const handleTouchEnd = (e: React.TouchEvent) => {
    if (!startX || !startY) return;

    const endX = e.changedTouches[0].clientX;
    const endY = e.changedTouches[0].clientY;

    const diffX = startX - endX;
    const diffY = startY - endY;

    // Only trigger swipe if horizontal movement is greater than vertical
    if (Math.abs(diffX) > Math.abs(diffY)) {
      const threshold = 50; // Minimum swipe distance

      if (diffX > threshold && onSwipeLeft) {
        onSwipeLeft();
      } else if (diffX < -threshold && onSwipeRight) {
        onSwipeRight();
      }
    }

    startX = 0;
    startY = 0;
  };

  return (
    <div
      className={cn(
        'touch-manipulation select-none',
        className
      )}
      onTouchStart={handleTouchStart}
      onTouchEnd={handleTouchEnd}
    >
      {children}
    </div>
  );
};

export { TouchButton, TouchCard, TouchInput, SwipeableCard };
