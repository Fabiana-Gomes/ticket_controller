document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('ticketModal');
    const content = document.getElementById('ticketDetalhes');
    const modalContent = modal?.querySelector('.modal-content');
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterCard = document.getElementById('filterCard');
    const filterIcon = document.getElementById('filterIcon');
    const filterForm = document.getElementById('filterForm');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const statusFilter = document.getElementById('statusFilter');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    // abrir modal
    function abrirModal(ticketId) {
        const postIt = document.querySelector(`.post-it[data-id="${ticketId}"]`);
        if (!postIt || !modalContent) return;

        modalContent.className = 'modal-content';
        const statusClasses = Array.from(postIt.classList).filter(c => c !== 'post-it');
        if (statusClasses.length > 0) {
            modalContent.classList.add(statusClasses[0]);
        }

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
                console.error('Erro ao carregar ticket:', error);
                content.innerHTML = `<div class="error">Erro ao carregar detalhes do ticket</div>`;
            });
    }

    document.querySelectorAll('.post-it').forEach(ticket => {
        const ticketId = ticket.dataset.id;
        if (ticketId) {
            ticket.addEventListener('click', () => abrirModal(ticketId));
        }
    });

    //fechar modal
    function fecharModal() {
        if (modal) {
            modal.style.display = 'none';
        }
    }

    document.addEventListener('click', e => {
        if (modal && modal.style.display === 'flex') {
            if (e.target.closest('.close-modal') || e.target === modal) {
                fecharModal();
            }
        }
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            fecharModal();
        }
    });

    // Filtros
    function toggleFilter(e) {
        e.stopPropagation();
        const isVisible = filterCard.style.display === 'block';
        filterCard.style.display = isVisible ? 'none' : 'block';
        filterIcon.classList.toggle('bi-chevron-down', !isVisible);
        filterIcon.classList.toggle('bi-chevron-up', isVisible);
    }

    function parseDate(dateString) {
        if (!dateString) return null;
        const [day, month, year] = dateString.split('/');
        return new Date(`${year}-${month}-${day}`);
    }

    function applyFilters(e) {
        e.preventDefault();
        const status = statusFilter.value;
        const start = startDate.value;
        const end = endDate.value;

        document.querySelectorAll('.post-it').forEach(ticket => {
            const ticketStatus = Array.from(ticket.classList).find(c => c !== 'post-it') || 'aberto';
            const ticketDateText = ticket.querySelector('.post-it-date')?.textContent || '';
            const ticketDate = parseDate(ticketDateText);

            let shouldShow = true;

            const statusGroups = {
                'concluido': ['resolvido', 'respondido', 'corrigido'],
                'pedagogico': ['livros-lucrativos', 'instrutor-comunicativo', 'classstudio', 'carteira-estudante'],
                'ead': ['om-digital', 'correcao-cursos', 'loja-virtual', 'ajustes-loja', 'instalacao-ead', 'site', 'ajustes-site', 'migracao', 'ead', 'app-cursos'],
                'gestor': ['om-digital', 'gestor-melhorias', 'gestor'],
                'financeiro': ['pagarme', 'edpay', 'sender'],
                'presencial': ['instalacao', 'correcao-cursos', 'melhoria-metodo', 'desinstalacao'],
                'comercial': ['crm', 'lp', 'bel'],
                'treinamentos': ['treinamento', 'treinamento-ferramentas'],
                'cancelado': ['cancelado'],
                'outros': ['sugestao', 'acompanhamento']
            };

            if (status) {
                shouldShow = statusGroups[status]?.includes(ticketStatus) || false;
            }

            if (start) {
                const filterStartDate = new Date(start);
                if (ticketDate instanceof Date && !isNaN(ticketDate) && ticketDate < filterStartDate) {
                    shouldShow = false;
                }
            }

            if (end) {
                const filterEndDate = new Date(end);
                if (ticketDate instanceof Date && !isNaN(ticketDate) && ticketDate > filterEndDate) {
                    shouldShow = false;
                }
            }

            ticket.style.display = shouldShow ? 'block' : 'none';
        });

        filterCard.style.display = 'none';
        filterIcon.classList.replace('bi-chevron-up', 'bi-chevron-down');
    }

    function resetFilters(e) {
        e.preventDefault();

        if (statusFilter) statusFilter.value = '';
        if (startDate) startDate.value = '';
        if (endDate) endDate.value = '';

        document.querySelectorAll('.post-it').forEach(ticket => {
            ticket.style.display = 'block';
        });

        if (filterCard) filterCard.style.display = 'none';
        if (filterIcon) {
            filterIcon.classList.remove('bi-chevron-up');
            filterIcon.classList.add('bi-chevron-down');
        }

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    }

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

    // cores 
    document.querySelectorAll('.post-it').forEach(postIt => {
        const style = getComputedStyle(postIt);
        const rgb = style.getPropertyValue('--status-rgb').trim();
        postIt.style.borderTop = `5px solid rgb(${rgb})`;

        const statusSpan = postIt.querySelector('.post-it-status');
        if (statusSpan) {
            statusSpan.className = 'post-it-status';
            const statusClasses = Array.from(postIt.classList).filter(c => c !== 'post-it');
            if (statusClasses.length > 0) {
                statusSpan.classList.add(statusClasses[0]);
            }

            statusSpan.style.color = `rgb(${rgb})`;
        }
    });
        // exibir em listagem
        const toggleViewBtn = document.getElementById('toggleViewBtn');
        const toggleViewIcon = document.getElementById('toggleViewIcon');
        const container = document.querySelector('.post-it-container');

        let isListView = false;

        toggleViewBtn?.addEventListener('click', () => {
            isListView = !isListView;
            container.classList.toggle('list-view', isListView);
            toggleViewIcon.classList.toggle('bi-grid', !isListView);
            toggleViewIcon.classList.toggle('bi-list', isListView);
    });
});