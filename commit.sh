#!/bin/bash

# Verifica se foi fornecido algum argumento
if [ $# -eq 0 ]; then
    echo "Uso: ./commit.sh <mensagem do commit>"
    exit 1
fi

# Adiciona todas as mudanças ao staging area
git add .

# Pega todos os argumentos passados e junta em uma única string
commit_message="$*"

# Realiza o commit com o comentário fornecido
git commit -m "$commit_message"