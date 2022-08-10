#!/bin/bash

# Run middleware pipeline
ansible-galaxy install -r requirements.yaml
ansible-playbook playbook.yaml -e"@infra.yaml" -e"@values.yaml"
