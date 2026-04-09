variable "namespace" {
  description = "K8s namespace for category"
  type        = string
  default     = "category"
}
variable "webhook_secret" {
  description = "secret for webhook v2"
  type        = string
  default     = "changeme"
}
variable "otlp_endpoint" {
  description = "OTLP collector endpoint"
  type        = string
  default     = "http://otel-collector:4318"
}
