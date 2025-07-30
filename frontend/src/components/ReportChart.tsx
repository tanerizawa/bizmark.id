'use client';

import { useEffect, useRef } from 'react';

interface ChartDataset {
  label?: string;
  data: number[];
  backgroundColor?: string | string[];
  borderColor?: string;
  tension?: number;
}

interface ChartData {
  labels: string[];
  datasets: ChartDataset[];
}

interface ReportChartProps {
  type: 'line' | 'bar' | 'doughnut' | 'pie';
  data: ChartData;
  options?: Record<string, unknown>;
  className?: string;
}

export default function ReportChart({ type, data, options = {}, className = '' }: ReportChartProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const chartRef = useRef<{ destroy: () => void } | null>(null);

  useEffect(() => {
    const initChart = async () => {
      if (!canvasRef.current) return;

      // Dynamically import Chart.js to avoid SSR issues
      const { Chart, registerables } = await import('chart.js');
      Chart.register(...registerables);

      // Destroy existing chart if it exists
      if (chartRef.current) {
        chartRef.current.destroy();
      }

      const ctx = canvasRef.current.getContext('2d');
      if (!ctx) return;

      const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom' as const,
          },
        },
      };

      chartRef.current = new Chart(ctx, {
        type,
        data,
        options: {
          ...defaultOptions,
          ...options,
        },
      }) as { destroy: () => void };
    };

    initChart();

    // Cleanup function
    return () => {
      if (chartRef.current) {
        chartRef.current.destroy();
      }
    };
  }, [type, data, options]);

  return (
    <div className={`relative ${className}`} style={{ height: '300px' }}>
      <canvas ref={canvasRef} />
    </div>
  );
}
