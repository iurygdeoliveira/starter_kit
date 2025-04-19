#!/bin/bash

# Verifica se há argumentos para commit
if [ $# -eq 0 ]; then
    # Se não houver argumentos, faz apenas o push
    echo "Realizando push das alterações..."
    git push
else
    # Se houver argumentos, executa o script commit.sh com os argumentos
    echo "Executando commit com os argumentos fornecidos..."
    ./commit.sh "$@"
    
    # Após o commit, realiza o push
    echo "Realizando push das alterações..."
    git push
    
    echo "Mudanças commitadas e enviadas para o GitHub"
fi