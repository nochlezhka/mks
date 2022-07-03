{{/* vim: set filetype=mustache: */}}

{{/*
Create a default qualified app name.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
If release name contains chart name it will be used as a full name.
*/}}
{{- define "name" -}}
{{- if .Values.name.override -}}
{{-   .Values.name.override | trunc 63 | trimSuffix "-" -}}
{{- else if .Values.name.useReleaseName -}}
{{-   .Release.Name | trunc 63 | trimSuffix "-" -}}
{{- else -}}
{{-   if contains .Chart.Name .Release.Name -}}
{{-     .Release.Name | trunc 63 | trimSuffix "-" -}}
{{-   else -}}
{{-     printf "%s-%s" .Release.Name .Chart.Name | trunc 63 | trimSuffix "-" -}}
{{-   end -}}
{{- end -}}
{{- end -}}

{{/* Labels */}}
{{- define "labels" -}}
name: {{ template "name" . }}
instance: {{ .Release.Name }}
chart: "{{ .Chart.Name }}-{{ .Chart.Version | replace "+" "_" }}"
{{- end -}}

{{/* Manage match labels for selector */}}
{{- define "matchLabels" -}}
name: {{ template "name" . }}
instance: {{ .Release.Name }}
{{- end -}}

{{/* Operator image */}}
{{- define "appImage" -}}
{{- printf "%s/%s:%s" .Values.registry.url .Values.app.image.path .Values.app.image.version -}}
{{- end -}}
