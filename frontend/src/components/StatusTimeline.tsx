'use client';

import { ApplicationStatus } from '@/types';

interface StatusTimelineProps {
  currentStatus: ApplicationStatus;
  submittedAt?: string;
  processedAt?: string;
  rejectionReason?: string;
  className?: string;
}

interface TimelineStep {
  status: ApplicationStatus;
  label: string;
  description: string;
  icon: React.ReactNode;
}

export default function StatusTimeline({ 
  currentStatus, 
  submittedAt, 
  processedAt, 
  rejectionReason,
  className = '' 
}: StatusTimelineProps) {
  const steps: TimelineStep[] = [
    {
      status: ApplicationStatus.DRAFT,
      label: 'Draft',
      description: 'Aplikasi dalam tahap persiapan',
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
      )
    },
    {
      status: ApplicationStatus.SUBMITTED,
      label: 'Dikirim',
      description: 'Aplikasi telah dikirim untuk diproses',
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
        </svg>
      )
    },
    {
      status: ApplicationStatus.UNDER_REVIEW,
      label: 'Sedang Ditinjau',
      description: 'Tim sedang meninjau dokumen dan informasi',
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>
      )
    },
    {
      status: ApplicationStatus.APPROVED,
      label: 'Disetujui',
      description: 'Aplikasi disetujui dan izin akan diterbitkan',
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      )
    }
  ];

  const getStepStatus = (stepStatus: ApplicationStatus) => {
    const statusOrder = [
      ApplicationStatus.DRAFT,
      ApplicationStatus.SUBMITTED,
      ApplicationStatus.UNDER_REVIEW,
      ApplicationStatus.APPROVED
    ];

    const currentIndex = statusOrder.indexOf(currentStatus);
    const stepIndex = statusOrder.indexOf(stepStatus);

    if (currentStatus === ApplicationStatus.REJECTED) {
      if (stepStatus === ApplicationStatus.SUBMITTED) return 'completed';
      if (stepStatus === ApplicationStatus.UNDER_REVIEW) return 'rejected';
      return 'pending';
    }

    if (stepIndex < currentIndex) return 'completed';
    if (stepIndex === currentIndex) return 'current';
    return 'pending';
  };

  const getStepClasses = (status: string) => {
    const baseClasses = "flex items-center justify-center w-8 h-8 rounded-full border-2 transition-colors";
    
    switch (status) {
      case 'completed':
        return `${baseClasses} bg-green-100 border-green-500 text-green-600`;
      case 'current':
        return `${baseClasses} bg-blue-100 border-blue-500 text-blue-600`;
      case 'rejected':
        return `${baseClasses} bg-red-100 border-red-500 text-red-600`;
      default:
        return `${baseClasses} bg-gray-100 border-gray-300 text-gray-400`;
    }
  };

  const getConnectorClasses = (stepStatus: ApplicationStatus, isLast: boolean) => {
    if (isLast) return 'hidden';
    
    const status = getStepStatus(stepStatus);
    const baseClasses = "w-full h-0.5 transition-colors";
    
    if (status === 'completed') return `${baseClasses} bg-green-500`;
    if (status === 'rejected') return `${baseClasses} bg-red-500`;
    return `${baseClasses} bg-gray-300`;
  };

  const formatDate = (dateString?: string) => {
    if (!dateString) return null;
    return new Date(dateString).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  return (
    <div className={`bg-white rounded-lg shadow p-6 ${className}`}>
      <h3 className="text-lg font-semibold text-gray-900 mb-6">Status Aplikasi</h3>
      
      <div className="space-y-6">
        {steps.map((step, index) => {
          const stepStatus = getStepStatus(step.status);
          const isLast = index === steps.length - 1;
          
          return (
            <div key={step.status} className="relative">
              <div className="flex items-start">
                {/* Step Icon */}
                <div className="flex flex-col items-center">
                  <div className={getStepClasses(stepStatus)}>
                    {stepStatus === 'completed' ? (
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                      </svg>
                    ) : stepStatus === 'rejected' ? (
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    ) : (
                      step.icon
                    )}
                  </div>
                  
                  {/* Connector Line */}
                  {!isLast && (
                    <div className="flex items-center justify-center w-8 mt-2">
                      <div className={getConnectorClasses(step.status, isLast)}></div>
                    </div>
                  )}
                </div>

                {/* Step Content */}
                <div className="ml-4 flex-1 pb-8">
                  <div className="flex items-center justify-between">
                    <h4 className={`font-medium ${
                      stepStatus === 'current' ? 'text-blue-900' : 
                      stepStatus === 'completed' ? 'text-green-900' :
                      stepStatus === 'rejected' ? 'text-red-900' :
                      'text-gray-500'
                    }`}>
                      {step.label}
                    </h4>
                    
                    {/* Timestamp */}
                    {step.status === ApplicationStatus.SUBMITTED && submittedAt && (
                      <span className="text-xs text-gray-500">
                        {formatDate(submittedAt)}
                      </span>
                    )}
                    {(step.status === ApplicationStatus.APPROVED || step.status === ApplicationStatus.REJECTED) && processedAt && (
                      <span className="text-xs text-gray-500">
                        {formatDate(processedAt)}
                      </span>
                    )}
                  </div>
                  
                  <p className={`text-sm mt-1 ${
                    stepStatus === 'current' ? 'text-blue-700' : 
                    stepStatus === 'completed' ? 'text-green-700' :
                    stepStatus === 'rejected' ? 'text-red-700' :
                    'text-gray-500'
                  }`}>
                    {step.description}
                  </p>

                  {/* Rejection Reason */}
                  {step.status === ApplicationStatus.UNDER_REVIEW && currentStatus === ApplicationStatus.REJECTED && rejectionReason && (
                    <div className="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                      <p className="text-sm font-medium text-red-800">Alasan Penolakan:</p>
                      <p className="text-sm text-red-700 mt-1">{rejectionReason}</p>
                    </div>
                  )}

                  {/* Current Status Indicator */}
                  {stepStatus === 'current' && (
                    <div className="mt-3 flex items-center">
                      <div className="w-2 h-2 bg-blue-500 rounded-full animate-pulse mr-2"></div>
                      <span className="text-xs text-blue-600 font-medium">Status saat ini</span>
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Additional Information */}
      <div className="mt-6 pt-6 border-t border-gray-200">
        <div className="text-sm text-gray-600">
          <p className="mb-2">
            <span className="font-medium">Estimasi Waktu Pemrosesan:</span> 7-14 hari kerja
          </p>
          <p>
            <span className="font-medium">Butuh Bantuan?</span>{' '}
            <a href="mailto:support@bizmark.id" className="text-blue-600 hover:text-blue-500">
              Hubungi Support
            </a>
          </p>
        </div>
      </div>
    </div>
  );
}
