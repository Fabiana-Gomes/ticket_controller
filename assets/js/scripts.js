function abrirModalTicket(ticketId) {
    const modal = document.getElementById('ticketModal');
    const content = document.getElementById('ticketDetalhes');
    const postIt = document.querySelector(`.post-it[onclick="abrirModalTicket(${ticketId})"]`);
    
    // Captura a posição do post-it clicado
    const rect = postIt.getBoundingClientRect();
    const clone = postIt.cloneNode(true);
    
    // Prepara o clone para animação
    clone.style.position = 'fixed';
    clone.style.top = `${rect.top}px`;
    clone.style.left = `${rect.left}px`;
    clone.style.width = `${rect.width}px`;
    clone.style.height = `${rect.height}px`;
    clone.style.margin = '0';
    clone.style.transform = 'rotate(0deg)';
    clone.style.transition = 'all 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28)';
    clone.style.zIndex = '1001';
    
    document.body.appendChild(clone);
    
    // Força o recálculo do layout
    void clone.offsetWidth;
    
    // Animação para o centro da tela
    const modalRect = modal.getBoundingClientRect();
    const targetTop = (window.innerHeight - rect.height) / 2;
    const targetLeft = (window.innerWidth - rect.width) / 2;
    
    clone.style.top = `${targetTop}px`;
    clone.style.left = `${targetLeft}px`;
    clone.style.transform = 'scale(1.2) rotate(0deg)';
    clone.style.boxShadow = '0 20px 50px rgba(0,0,0,0.3)';
    
    // Mostra o modal após a animação
    setTimeout(() => {
        modal.style.display = 'block';
        content.innerHTML = '<div class="loading">Carregando...</div>';
        clone.style.opacity = '0';
        
        fetch(`get_ticket.php?id=${ticketId}`)
            .then(response => response.text())
            .then(data => {
                content.innerHTML = data;
                setTimeout(() => clone.remove(), 300);
            })
            .catch(error => {
                content.innerHTML = `<div class="error">${error.message}</div>`;
                clone.remove();
            });
    }, 500);
}

function fecharModal() {
    const modal = document.getElementById('ticketModal');
    modal.style.animation = 'fadeOut 0.3s';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.animation = '';
    }, 300);
}