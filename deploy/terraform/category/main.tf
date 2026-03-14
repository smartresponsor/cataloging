terraform {
  required_version = ">= 1.5.0"
  required_providers {
    kubernetes = {
      source  = "hashicorp/kubernetes"
      version = ">= 2.0.0"
    }
  }
}
variable "namespace" { type = string }
resource "kubernetes_namespace" "category" {
  metadata { name = var.namespace }
}
