class Pagination {
    constructor(options) {
        this.itemsPerPage = options.itemsPerPage || 10;
        this.tableBodySelector = options.tableBody;
        this.paginationContainerSelector = options.paginationContainer;
        this.showingTextSelector = options.showingText;
        this.currentPage = 1;

        this.tableBody = document.querySelector(this.tableBodySelector);
        this.paginationContainer = document.querySelector(this.paginationContainerSelector);
        this.showingText = document.querySelector(this.showingTextSelector);

        if (!this.tableBody) {
            console.error('Table body not found');
            return;
        }

        this.allRows = Array.from(this.tableBody.querySelectorAll('tr'));
        this.totalItems = this.allRows.length;
        this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

        this.init();
    }

    init() {
        this.showPage(1);
        this.renderPagination();
    }

    showPage(pageNumber) {
        this.currentPage = pageNumber;
        this.allRows.forEach(row => row.classList.add('hidden'));

        const start = (pageNumber - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;

        for (let i = start; i < end && i < this.allRows.length; i++) {
            this.allRows[i].classList.remove('hidden');
        }

        this.updateShowingText();

        this.updatePaginationButtons();
    }

    updateShowingText() {
        if (!this.showingText) return;

        const start = (this.currentPage - 1) * this.itemsPerPage + 1;
        const end = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);

        this.showingText.textContent = `Showing ${start}–${end} of ${this.totalItems}`;
    }

    renderPagination() {
        if (!this.paginationContainer) return;

        this.paginationContainer.innerHTML = '';

        if (this.totalPages <= 1) {
            this.paginationContainer.parentElement.classList.add('hidden');
            return;
        }

        this.paginationContainer.parentElement.classList.remove('hidden');

        const prevBtn = this.createButton('prev', `
            <span class="sr-only">Previous</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        `, 'relative inline-flex items-center px-2 py-2 rounded-l-lg border border-gray-600 bg-gray-800 text-sm font-medium text-gray-500 hover:bg-gray-900');

        prevBtn.addEventListener('click', () => this.goToPage(this.currentPage - 1));
        this.paginationContainer.appendChild(prevBtn);

        for (let i = 1; i <= this.totalPages; i++) {
            const pageBtn = this.createButton(`page-${i}`, i,
                'relative inline-flex items-center px-4 py-2 border border-gray-600 bg-gray-800 text-sm font-medium text-gray-300 hover:bg-gray-900'
            );

            pageBtn.addEventListener('click', () => this.goToPage(i));
            this.paginationContainer.appendChild(pageBtn);
        }

        const nextBtn = this.createButton('next', `
            <span class="sr-only">Next</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        `, 'relative inline-flex items-center px-2 py-2 rounded-r-lg border border-gray-600 bg-gray-800 text-sm font-medium text-gray-500 hover:bg-gray-900');

        nextBtn.addEventListener('click', () => this.goToPage(this.currentPage + 1));
        this.paginationContainer.appendChild(nextBtn);

        this.updatePaginationButtons();
    }

    createButton(id, content, className) {
        const button = document.createElement('button');
        button.setAttribute('data-page-btn', id);
        button.className = className;
        button.innerHTML = content;
        return button;
    }

    updatePaginationButtons() {
        if (!this.paginationContainer) return;

        const prevBtn = this.paginationContainer.querySelector('[data-page-btn="prev"]');
        if (prevBtn) {
            if (this.currentPage === 1) {
                prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                prevBtn.disabled = false;
            }
        }

        const nextBtn = this.paginationContainer.querySelector('[data-page-btn="next"]');
        if (nextBtn) {
            if (this.currentPage === this.totalPages) {
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                nextBtn.disabled = false;
            }
        }

        for (let i = 1; i <= this.totalPages; i++) {
            const pageBtn = this.paginationContainer.querySelector(`[data-page-btn="page-${i}"]`);
            if (pageBtn) {
                if (i === this.currentPage) {
                    pageBtn.className = 'relative inline-flex items-center px-4 py-2 border border-gray-600 bg-indigo-600 text-sm font-medium text-white';
                } else {
                    pageBtn.className = 'relative inline-flex items-center px-4 py-2 border border-gray-600 bg-gray-800 text-sm font-medium text-gray-300 hover:bg-gray-900';
                }
            }
        }
    }

    goToPage(pageNumber) {
        if (pageNumber < 1 || pageNumber > this.totalPages) return;
        this.showPage(pageNumber);
    }
}