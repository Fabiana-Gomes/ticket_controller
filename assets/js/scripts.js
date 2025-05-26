document.addEventListener('DOMContentLoaded', function() {
    // ============ FUNÇÕES DO MODAL ============
    function abrirModalTicket(ticketId) {
        const modal = document.getElementById('ticketModal');
        const content = document.getElementById('ticketDetalhes');
        const postIt = document.querySelector(`.post-it[onclick="abrirModalTicket(${ticketId})"]`);
        
        if (!postIt) return;
        
        const statusClass = postIt.classList.contains('aberto') ? 'aberto' :
                          postIt.classList.contains('em-andamento') ? 'em-andamento' :
                          postIt.classList.contains('resolvido') ? 'resolvido' : 'fechado';
        
        modal.querySelector('.modal-content').className = `modal-content ${statusClass}`;
        modal.style.display = 'flex';
        content.innerHTML = '<div class="loading">Carregando...</div>';
        
        fetch(`get_ticket.php?id=${ticketId}`)
            .then(response => {
                if (!response.ok) throw new Error('Erro na rede');
                return response.text();
            })
            .then(data => {
                content.innerHTML = data;
            })
            .catch(error => {
                console.error('Erro:', error);
                content.innerHTML = `<div class="error">Erro ao carregar ticket</div>`;
            });
    }

    function fecharModal() {
        document.getElementById('ticketModal').style.display = 'none';
    }

    // ============ FUNÇÕES DO FILTRO ============
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterCard = document.getElementById('filterCard');
    const filterIcon = document.getElementById('filterIcon');
    const filterForm = document.getElementById('filterForm');
    const resetFiltersBtn = document.getElementById('resetFilters');

    // Mostrar/ocultar filtro
    function toggleFilter(e) {
        e.stopPropagation();
        const isVisible = filterCard.style.display === 'block';
        filterCard.style.display = isVisible ? 'none' : 'block';
        filterIcon.classList.toggle('bi-chevron-down', !isVisible);
        filterIcon.classList.toggle('bi-chevron-up', isVisible);
    }

    // Fechar ao clicar fora
    document.addEventListener('click', function(e) {
        if (!filterCard.contains(e.target) && e.target !== filterToggleBtn) {
            filterCard.style.display = 'none';
            filterIcon.classList.replace('bi-chevron-up', 'bi-chevron-down');
        }
    });

    // Converter data dd/mm/yyyy para yyyy-mm-dd
    function parseDate(dateString) {
        if (!dateString) return null;
        const [day, month, year] = dateString.split('/');
        return new Date(`${year}-${month}-${day}`);
    }

    // Aplicar filtros
    function applyFilters(e) {
        e.preventDefault();
        const status = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        document.querySelectorAll('.post-it').forEach(ticket => {
            const ticketStatus = ticket.classList.contains('aberto') ? 'aberto' :
                               ticket.classList.contains('em-andamento') ? 'em-andamento' :
                               ticket.classList.contains('resolvido') ? 'resolvido' : 'fechado';
            
            const ticketDateText = ticket.querySelector('.post-it-date').textContent;
            const ticketDate = parseDate(ticketDateText);
            
            let shouldShow = true;
            
            // Filtro por status
            if (status && ticketStatus !== status) {
                shouldShow = false;
            }
            
            // Filtro por data
            if (startDate) {
                const filterStartDate = new Date(startDate);
                if (ticketDate < filterStartDate) shouldShow = false;
            }
            
            if (endDate) {
                const filterEndDate = new Date(endDate);
                if (ticketDate > filterEndDate) shouldShow = false;
            }
            
            ticket.style.display = shouldShow ? 'block' : 'none';
        });
        
        filterCard.style.display = 'none';
        filterIcon.classList.replace('bi-chevron-up', 'bi-chevron-down');
    }

    // Resetar filtros
    function resetFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        
        document.querySelectorAll('.post-it').forEach(ticket => {
            ticket.style.display = 'block';
        });
    }

    // ============ INICIALIZAÇÃO ============
    if (filterToggleBtn && filterCard) {
        filterCard.style.display = 'none';
        filterToggleBtn.addEventListener('click', toggleFilter);
    }

    if (filterForm) {
        filterForm.addEventListener('submit', applyFilters);
    }

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', resetFilters);
    }

    // Eventos do modal
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('ticketModal');
        if (e.target.closest('.close-modal') || e.target === modal) {
            fecharModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('ticketModal').style.display === 'flex') {
            fecharModal();
        }
    });

    // Eventos dos tickets
    document.querySelectorAll('.post-it').forEach(ticket => {
        const onclickAttr = ticket.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('abrirModalTicket')) {
            const ticketId = onclickAttr.match(/\d+/)[0];
            ticket.addEventListener('click', () => abrirModalTicket(ticketId));
        }
    });
});