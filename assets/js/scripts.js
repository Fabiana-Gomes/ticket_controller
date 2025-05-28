document.addEventListener('DOMContentLoaded', function () {

    //abrir modal

    
    function abrirModalTicket(ticketId) {
        const modal = document.getElementById('ticketModal');
        const content = document.getElementById('ticketDetalhes');
        const postIt = document.querySelector(`.post-it[data-id="${ticketId}"]`);

        if (!postIt) {
            console.error('Ticket não encontrado:', ticketId);
            return;
        }
        const statusClass = postIt.classList.contains('aberto') ? 'aberto' :
            postIt.classList.contains('em-andamento') ? 'em-andamento' :
                postIt.classList.contains('resolvido') ? 'resolvido' : 'fechado';

        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.className = `modal-content ${statusClass}`;
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
            ticket.addEventListener('click', () => abrirModalTicket(ticketId));
        }
    });

    //fechar modal

    function fecharModal() {
        const modal = document.getElementById('ticketModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('ticketModal');
        if (modal && modal.style.display === 'flex') {
            if (e.target.closest('.close-modal') || e.target === modal) {
                fecharModal();
            }
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('ticketModal');
            if (modal && modal.style.display === 'flex') {
                fecharModal();
            }
        }
    });


    // filtro

    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterCard = document.getElementById('filterCard');
    const filterIcon = document.getElementById('filterIcon');
    const filterForm = document.getElementById('filterForm');
    const resetFiltersBtn = document.getElementById('resetFilters');

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
        const status = document.getElementById('statusFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        document.querySelectorAll('.post-it').forEach(ticket => {
            const ticketStatus =
                ticket.classList.contains('aberto') ? 'aberto' :
                    ticket.classList.contains('em-andamento') ? 'em-andamento' :
                        ticket.classList.contains('resolvido') ? 'resolvido' :
                            'fechado';

            const ticketDateText = ticket.querySelector('.post-it-date')?.textContent || '';
            const ticketDate = parseDate(ticketDateText);

            let shouldShow = true;

            if (status && ticketStatus !== status) {
                shouldShow = false;
            }

            if (startDate) {
                const filterStartDate = new Date(startDate);
                if (ticketDate instanceof Date && !isNaN(ticketDate) && ticketDate < filterStartDate) {
                    shouldShow = false;
                }
            }

            if (endDate) {
                const filterEndDate = new Date(endDate);
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

    document.addEventListener('click', function (e) {
        if (!filterCard.contains(e.target) && e.target !== filterToggleBtn) {
            filterCard.style.display = 'none';
            filterIcon.classList.replace('bi-chevron-up', 'bi-chevron-down');
        }
    });

 // cores 
 
document.querySelectorAll('.post-it').forEach(postIt => {

    const style = getComputedStyle(postIt);
    const rgb = style.getPropertyValue('--status-rgb').trim();
    postIt.style.borderTop = `5px solid rgb(${rgb})`;
    
    const statusSpan = postIt.querySelector('.post-it-status');
    if(statusSpan) {
        statusSpan.className = 'post-it-status';
        const statusClasses = Array.from(postIt.classList).filter(c => c !== 'post-it');
        if(statusClasses.length > 0) {
            statusSpan.classList.add(statusClasses[0]);
        }

        statusSpan.style.color = `rgb(${rgb})`;
    }
});
});
