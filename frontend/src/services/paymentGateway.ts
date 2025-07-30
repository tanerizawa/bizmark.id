/**
 * Payment Gateway Integration Service
 * Handles payment processing with Midtrans and other payment providers
 */

export interface PaymentConfig {
  baseUrl: string;
  serverKey: string;
  clientKey: string;
  environment: 'sandbox' | 'production';
}

export interface PaymentRequest {
  orderId: string;
  amount: number;
  currency: 'IDR';
  customerDetails: {
    firstName: string;
    lastName?: string;
    email: string;
    phone: string;
  };
  itemDetails: Array<{
    id: string;
    price: number;
    quantity: number;
    name: string;
    category?: string;
  }>;
  businessId?: string;
  licenseType?: string;
  description?: string;
}

export interface PaymentResponse {
  success: boolean;
  message: string;
  data?: {
    token: string;
    redirectUrl: string;
    orderId: string;
    status: string;
    amount: number;
    paymentType?: string;
    transactionId?: string;
  };
  error?: string;
  timestamp: string;
}

export interface PaymentStatusResponse {
  success: boolean;
  message: string;
  data?: {
    orderId: string;
    transactionStatus: 'pending' | 'settlement' | 'capture' | 'deny' | 'cancel' | 'expire' | 'failure';
    paymentType: string;
    transactionId: string;
    grossAmount: string;
    fraudStatus?: string;
    statusMessage?: string;
    transactionTime?: string;
    settlementTime?: string;
  };
  error?: string;
  timestamp: string;
}

export interface PaymentMethod {
  id: string;
  name: string;
  type: 'bank_transfer' | 'credit_card' | 'e_wallet' | 'over_the_counter' | 'qris';
  provider: string;
  logo: string;
  fee: number;
  processingTime: string;
  description: string;
  isActive: boolean;
}

class PaymentGatewayService {
  private config: PaymentConfig;

  constructor(config: PaymentConfig) {
    this.config = config;
  }

  /**
   * Create payment transaction
   */
  async createPayment(paymentRequest: PaymentRequest): Promise<PaymentResponse> {
    try {
      const payload = {
        transaction_details: {
          order_id: paymentRequest.orderId,
          gross_amount: paymentRequest.amount,
        },
        credit_card: {
          secure: true,
        },
        customer_details: {
          first_name: paymentRequest.customerDetails.firstName,
          last_name: paymentRequest.customerDetails.lastName || '',
          email: paymentRequest.customerDetails.email,
          phone: paymentRequest.customerDetails.phone,
        },
        item_details: paymentRequest.itemDetails.map(item => ({
          id: item.id,
          price: item.price,
          quantity: item.quantity,
          name: item.name,
          category: item.category || 'license',
        })),
        custom_field1: paymentRequest.businessId || '',
        custom_field2: paymentRequest.licenseType || '',
        custom_field3: paymentRequest.description || '',
      };

      const response = await fetch(`${this.config.baseUrl}/charge`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Basic ${Buffer.from(this.config.serverKey + ':').toString('base64')}`,
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error_messages?.[0] || `Payment creation failed: ${response.statusText}`);
      }

      return {
        success: true,
        message: 'Payment created successfully',
        data: {
          token: data.token,
          redirectUrl: data.redirect_url,
          orderId: data.order_id,
          status: data.transaction_status,
          amount: paymentRequest.amount,
          paymentType: data.payment_type,
          transactionId: data.transaction_id,
        },
        timestamp: new Date().toISOString(),
      };
    } catch (error) {
      return {
        success: false,
        message: 'Payment creation failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Check payment status
   */
  async getPaymentStatus(orderId: string): Promise<PaymentStatusResponse> {
    try {
      const response = await fetch(`${this.config.baseUrl}/${orderId}/status`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Basic ${Buffer.from(this.config.serverKey + ':').toString('base64')}`,
        },
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error_messages?.[0] || `Status check failed: ${response.statusText}`);
      }

      return {
        success: true,
        message: 'Payment status retrieved successfully',
        data: {
          orderId: data.order_id,
          transactionStatus: data.transaction_status,
          paymentType: data.payment_type,
          transactionId: data.transaction_id,
          grossAmount: data.gross_amount,
          fraudStatus: data.fraud_status,
          statusMessage: data.status_message,
          transactionTime: data.transaction_time,
          settlementTime: data.settlement_time,
        },
        timestamp: new Date().toISOString(),
      };
    } catch (error) {
      return {
        success: false,
        message: 'Payment status check failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Cancel payment transaction
   */
  async cancelPayment(orderId: string): Promise<PaymentResponse> {
    try {
      const response = await fetch(`${this.config.baseUrl}/${orderId}/cancel`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Basic ${Buffer.from(this.config.serverKey + ':').toString('base64')}`,
        },
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error_messages?.[0] || `Payment cancellation failed: ${response.statusText}`);
      }

      return {
        success: true,
        message: 'Payment cancelled successfully',
        data: {
          token: '',
          redirectUrl: '',
          orderId: data.order_id,
          status: data.transaction_status,
          amount: 0,
          transactionId: data.transaction_id,
        },
        timestamp: new Date().toISOString(),
      };
    } catch (error) {
      return {
        success: false,
        message: 'Payment cancellation failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Get available payment methods
   */
  async getPaymentMethods(): Promise<PaymentMethod[]> {
    // This would typically come from your backend or payment provider
    return [
      {
        id: 'bank_transfer',
        name: 'Transfer Bank',
        type: 'bank_transfer',
        provider: 'Midtrans',
        logo: '/images/payment/bank-transfer.png',
        fee: 4000,
        processingTime: '1-2 hari kerja',
        description: 'Transfer melalui ATM, Internet Banking, atau Mobile Banking',
        isActive: true,
      },
      {
        id: 'credit_card',
        name: 'Kartu Kredit/Debit',
        type: 'credit_card',
        provider: 'Midtrans',
        logo: '/images/payment/credit-card.png',
        fee: 0,
        processingTime: 'Instan',
        description: 'Visa, Mastercard, JCB, Amex',
        isActive: true,
      },
      {
        id: 'gopay',
        name: 'GoPay',
        type: 'e_wallet',
        provider: 'GoPay',
        logo: '/images/payment/gopay.png',
        fee: 0,
        processingTime: 'Instan',
        description: 'Bayar dengan GoPay',
        isActive: true,
      },
      {
        id: 'ovo',
        name: 'OVO',
        type: 'e_wallet',
        provider: 'OVO',
        logo: '/images/payment/ovo.png',
        fee: 0,
        processingTime: 'Instan',
        description: 'Bayar dengan OVO',
        isActive: true,
      },
      {
        id: 'dana',
        name: 'DANA',
        type: 'e_wallet',
        provider: 'DANA',
        logo: '/images/payment/dana.png',
        fee: 0,
        processingTime: 'Instan',
        description: 'Bayar dengan DANA',
        isActive: true,
      },
      {
        id: 'shopeepay',
        name: 'ShopeePay',
        type: 'e_wallet',
        provider: 'ShopeePay',
        logo: '/images/payment/shopeepay.png',
        fee: 0,
        processingTime: 'Instan',
        description: 'Bayar dengan ShopeePay',
        isActive: true,
      },
      {
        id: 'qris',
        name: 'QRIS',
        type: 'qris',
        provider: 'QRIS',
        logo: '/images/payment/qris.png',
        fee: 0,
        processingTime: 'Instan',
        description: 'Scan QR Code untuk pembayaran',
        isActive: true,
      },
      {
        id: 'indomaret',
        name: 'Indomaret',
        type: 'over_the_counter',
        provider: 'Indomaret',
        logo: '/images/payment/indomaret.png',
        fee: 5000,
        processingTime: '1-2 hari kerja',
        description: 'Bayar di kasir Indomaret terdekat',
        isActive: true,
      },
      {
        id: 'alfamart',
        name: 'Alfamart',
        type: 'over_the_counter',
        provider: 'Alfamart',
        logo: '/images/payment/alfamart.png',
        fee: 5000,
        processingTime: '1-2 hari kerja',
        description: 'Bayar di kasir Alfamart terdekat',
        isActive: true,
      },
    ];
  }

  /**
   * Calculate total payment amount including fees
   */
  calculateTotalAmount(baseAmount: number, paymentMethodId: string): Promise<{
    baseAmount: number;
    fee: number;
    totalAmount: number;
    paymentMethod: PaymentMethod;
  }> {
    return this.getPaymentMethods().then(methods => {
      const paymentMethod = methods.find(m => m.id === paymentMethodId);
      if (!paymentMethod) {
        throw new Error('Payment method not found');
      }

      const fee = paymentMethod.fee;
      const totalAmount = baseAmount + fee;

      return {
        baseAmount,
        fee,
        totalAmount,
        paymentMethod,
      };
    });
  }

  /**
   * Validate webhook signature (should be called server-side)
   */
  validateWebhookSignature(signature: string, orderId: string, statusCode: string, grossAmount: string): boolean {
    // Note: This validation should be performed on the server side for security
    console.warn('Webhook signature validation should be performed on the server side');
    
    // For now, return true as this is a client-side implementation
    // In production, this should be handled by a server-side API endpoint
    return signature === `${orderId}${statusCode}${grossAmount}`;
  }
}

// Create singleton instance
let paymentGatewayInstance: PaymentGatewayService | null = null;

export const createPaymentGatewayService = (config: PaymentConfig): PaymentGatewayService => {
  if (!paymentGatewayInstance) {
    paymentGatewayInstance = new PaymentGatewayService(config);
  }
  return paymentGatewayInstance;
};

export const getPaymentGatewayService = (): PaymentGatewayService => {
  if (!paymentGatewayInstance) {
    throw new Error('Payment Gateway service not initialized. Call createPaymentGatewayService first.');
  }
  return paymentGatewayInstance;
};

export default PaymentGatewayService;
