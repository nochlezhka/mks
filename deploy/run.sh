#!/bin/bash

#
# TODO: This should be a part of GitHub actions and separate repo
#
ansible-galaxy install  geerlingguy.docker
ansible-playbook -i hosts playbook.yaml