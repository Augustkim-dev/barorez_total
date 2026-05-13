import { z } from 'zod';

export const PrinterCapabilitySchema = z.enum(['kitchen', 'counter', 'bar']);

// Client → Server
export const AuthMessageSchema = z.object({
  type: z.literal('auth'),
  client_id: z.number().int().positive(),
  token: z.string().min(1),
  capabilities: z.array(PrinterCapabilitySchema).min(1),
  app_version: z.string().optional(),
  last_known_job_id: z.number().int().nonnegative().optional(),
});

export const AckMessageSchema = z.object({
  type: z.literal('ack'),
  job_id: z.number().int().positive(),
  status: z.enum(['printed', 'failed']),
  printed_at: z.string().optional(),
  error_code: z.string().optional(),
  error_message: z.string().optional(),
});

export const PongMessageSchema = z.object({
  type: z.literal('pong'),
});

export const ClientMessageSchema = z.discriminatedUnion('type', [
  AuthMessageSchema,
  AckMessageSchema,
  PongMessageSchema,
]);

export type ClientMessage = z.infer<typeof ClientMessageSchema>;
export type AuthMessage = z.infer<typeof AuthMessageSchema>;
export type AckMessage = z.infer<typeof AckMessageSchema>;

// Server → Client
export interface AuthOkMessage {
  type: 'auth_ok';
  client_id: number;
  shop_id: number;
}

export interface AuthFailMessage {
  type: 'auth_fail';
  reason: string;
}

export interface PrintJobMessage {
  type: 'print_job';
  job_id: number;
  shop_id: number;
  printer_type: 'kitchen' | 'counter' | 'bar';
  payload: unknown;
}

export interface PingMessage {
  type: 'ping';
}

export type ServerMessage = AuthOkMessage | AuthFailMessage | PrintJobMessage | PingMessage;
