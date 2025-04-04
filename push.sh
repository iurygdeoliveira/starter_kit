#!/bin/bash

# Verifica se há argumentos para commit
if [ $# -eq 0 ]; then
    # Se não houver argumentos, faz apenas o push
    echo "Realizando push das alterações..."
    git push
else
    # Se houver argumentos, faz commit e push
    # Adiciona todas as mudanças ao staging area
    git add .

    # Pega todos os argumentos passados e junta em uma única string
    commit_message="$*"

    # Realiza o commit com o comentário fornecido
    git commit -m "$commit_message"

    # Envia as mudanças para o repositório remoto
    git push

    echo "Mudanças commitadas e enviadas para o GitHub"
fi