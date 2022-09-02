#!/bin/bash

# Run middleware pipeline
#ansible-galaxy install -r requirements.yaml
#ansible-playbook playbook.yaml -e"@infra.yaml" -e"@values.yaml"


ansible-galaxy install  geerlingguy.docker
ansible-playbook playbook.yaml
