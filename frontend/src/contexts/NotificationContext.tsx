'use client';

import { createContext, useContext, useEffect, useState, ReactNode, useCallback } from 'react';

export interface Notification {
  id: string;
  type: 'info' | 'success' | 'warning' | 'error';
  title: string;
  message: string;
  timestamp: Date;
  read: boolean;
  actionUrl?: string;
  actionLabel?: string;
}

interface NotificationContextType {
  notifications: Notification[];
  unreadCount: number;
  addNotification: (notification: Omit<Notification, 'id' | 'timestamp' | 'read'>) => void;
  markAsRead: (id: string) => void;
  markAllAsRead: () => void;
  removeNotification: (id: string) => void;
  clearAll: () => void;
}

const NotificationContext = createContext<NotificationContextType | undefined>(undefined);

export function useNotifications() {
  const context = useContext(NotificationContext);
  if (context === undefined) {
    throw new Error('useNotifications must be used within a NotificationProvider');
  }
  return context;
}

interface NotificationProviderProps {
  children: ReactNode;
}

export function NotificationProvider({ children }: NotificationProviderProps) {
  const [notifications, setNotifications] = useState<Notification[]>([]);

  // Initialize with some sample notifications
  useEffect(() => {
    const sampleNotifications: Notification[] = [
      {
        id: '1',
        type: 'success',
        title: 'Aplikasi Disetujui',
        message: 'Aplikasi izin usaha Toko Berkah telah disetujui',
        timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
        read: false,
        actionUrl: '/dashboard/applications/1',
        actionLabel: 'Lihat Detail'
      },
      {
        id: '2',
        type: 'warning',
        title: 'Dokumen Diperlukan',
        message: 'Upload dokumen NPWP untuk melengkapi aplikasi',
        timestamp: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000), // 1 day ago
        read: false,
        actionUrl: '/dashboard/applications/2',
        actionLabel: 'Upload Dokumen'
      },
      {
        id: '3',
        type: 'info',
        title: 'Status Update',
        message: 'Aplikasi izin lokasi sedang dalam tahap review',
        timestamp: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000), // 3 days ago
        read: true,
        actionUrl: '/dashboard/applications/3',
        actionLabel: 'Cek Status'
      }
    ];

    setNotifications(sampleNotifications);
  }, []);

  const addNotification = useCallback((notification: Omit<Notification, 'id' | 'timestamp' | 'read'>) => {
    const newNotification: Notification = {
      ...notification,
      id: Date.now().toString(),
      timestamp: new Date(),
      read: false
    };

    setNotifications(prev => [newNotification, ...prev]);

    // Auto remove after 10 seconds for temporary notifications
    if (notification.type === 'success' || notification.type === 'info') {
      setTimeout(() => {
        setNotifications(prev => prev.filter(n => n.id !== newNotification.id));
      }, 10000);
    }
  }, []);

  // Simulate real-time notifications
  useEffect(() => {
    const interval = setInterval(() => {
      // Randomly add new notifications (simulation)
      if (Math.random() < 0.1) { // 10% chance every 30 seconds
        const sampleMessages = [
          {
            type: 'info' as const,
            title: 'Update Status',
            message: 'Status aplikasi izin Anda telah diperbarui'
          },
          {
            type: 'success' as const,
            title: 'Pembayaran Berhasil',
            message: 'Pembayaran biaya administrasi telah dikonfirmasi'
          },
          {
            type: 'warning' as const,
            title: 'Reminder',
            message: 'Jangan lupa lengkapi dokumen pendukung'
          }
        ];

        const randomMessage = sampleMessages[Math.floor(Math.random() * sampleMessages.length)];
        
        addNotification({
          type: randomMessage.type,
          title: randomMessage.title,
          message: randomMessage.message
        });
      }
    }, 30000); // Check every 30 seconds

    return () => clearInterval(interval);
  }, [addNotification]);

  const markAsRead = (id: string) => {
    setNotifications(prev =>
      prev.map(notification =>
        notification.id === id ? { ...notification, read: true } : notification
      )
    );
  };

  const markAllAsRead = () => {
    setNotifications(prev =>
      prev.map(notification => ({ ...notification, read: true }))
    );
  };

  const removeNotification = (id: string) => {
    setNotifications(prev => prev.filter(notification => notification.id !== id));
  };

  const clearAll = () => {
    setNotifications([]);
  };

  const unreadCount = notifications.filter(n => !n.read).length;

  const value = {
    notifications,
    unreadCount,
    addNotification,
    markAsRead,
    markAllAsRead,
    removeNotification,
    clearAll
  };

  return (
    <NotificationContext.Provider value={value}>
      {children}
    </NotificationContext.Provider>
  );
}
