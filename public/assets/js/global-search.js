class GlobalSearch {
    constructor() {
        // Khởi tạo các phần tử DOM
        this.searchInput = document.getElementById('search-input');
        this.searchResults = document.getElementById('search-results');
        
        // Tạo container kết quả nếu chưa tồn tại
        if (!this.searchResults) {
            this.searchResults = document.createElement('div');
            this.searchResults.id = 'search-results';
            document.body.appendChild(this.searchResults);
        }
        
        // Khởi tạo sự kiện
        if (this.searchInput) {
            this.initializeEvents();
            console.log('Đã khởi tạo chức năng tìm kiếm');
        } else {
            console.error('Không tìm thấy ô tìm kiếm');
        }
    }
    
    initializeEvents() {
        // Tự động tìm kiếm khi nhập (với debounce)
        let searchTimeout;
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.handleSearch(e);
            }, 300);
        });
        
        // Tìm kiếm khi nhấn Enter
        this.searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                this.handleSearch(e);
            }
        });
    }
    
    async handleSearch(e) {
        e.preventDefault();
        
        const keyword = this.searchInput.value.trim().toLowerCase();
        if (!keyword) {
            this.clearResults();
            return;
        }
        
        // Hiển thị đang tải
        this.searchResults.innerHTML = 'Đang tìm kiếm...';
        this.searchResults.style.display = 'block';
        
        try {
            // Tìm kiếm tất cả các phần tử có thể chứa nội dung cần tìm
            const allElements = Array.from(document.querySelectorAll('h2, h3, h4, p, a, div, span, li'));
            
            // Lọc các phần tử có chứa từ khóa
            const foundElements = allElements.filter(element => {
                // Bỏ qua các phần tử ẩn và các phần tử không hiển thị
                if (element.offsetParent === null || element.textContent.trim() === '') {
                    return false;
                }
                
                // Kiểm tra nội dung có chứa từ khóa không
                const text = element.textContent.toLowerCase();
                return text.includes(keyword);
            });
            
            // Lấy các phần tử gốc (tránh trùng lặp)
            const uniqueElements = [];
            const addedTexts = new Set();
            
            foundElements.forEach(element => {
                // Lấy phần tử cha gần nhất có class hoặc id
                const parent = element.closest('[class], [id]') || element;
                const text = parent.textContent.trim().substring(0, 100); // Lấy 100 ký tự đầu
                
                if (!addedTexts.has(text) && text.length > 10) { // Chỉ lấy các phần tử có nội dung đủ dài
                    addedTexts.add(text);
                    uniqueElements.push({
                        element: parent,
                        text: text
                    });
                }
            });
            
            // Hiển thị kết quả
            let html = `<h3>Kết quả tìm kiếm cho: "${keyword}"</h3>`;
            
            if (uniqueElements.length > 0) {
                html += '<ul class="search-results-list">';
                uniqueElements.slice(0, 10).forEach(item => { // Giới hạn 10 kết quả
                    const link = item.element.closest('a')?.href || '#';
                    const title = item.element.tagName.toLowerCase() === 'a' ? 
                        item.element.textContent : 
                        (item.element.querySelector('a')?.textContent || item.text);
                    
                    html += `
                        <li>
                            <a href="${link}" style="color: #0066cc; text-decoration: none;">
                                ${title}
                            </a>
                            <p style="margin: 5px 0 15px 0; color: #666; font-size: 0.9em;">
                                ${item.text}...
                            </p>
                        </li>`;
                });
                html += '</ul>';
            } else {
                html += '<p>Không tìm thấy kết quả nào phù hợp.</p>';
            }
            
            this.searchResults.innerHTML = html;
        } catch (error) {
            console.error('Lỗi khi tìm kiếm:', error);
            this.searchResults.innerHTML = 'Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.';
        }
    }
    
    displayResults(data, keyword) {
        if (!this.searchResults) {
            this.createResultsContainer();
        }
        
        let html = `
            <div class="search-results-container">
                <div class="search-results-header">
                    <h3>Kết quả tìm kiếm cho: "${keyword}"</h3>
                    <button class="close-search-results" onclick="globalSearch.closeResults()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="search-results-body">`;
        
        // Hiển thị sản phẩm
        if (data.products && data.products.length > 0) {
            html += `
                <div class="search-section">
                    <h4><i class="fas fa-box"></i> Sản phẩm (${data.products.length})</h4>
                    <div class="search-products">
                        ${data.products.map(product => `
                            <a href="/PJ1/public/store/product/${product.slug || product.id}" class="search-item">
                                <img src="/PJ1/public/assets/img/${product.image || 'no-image.jpg'}" alt="${product.name}" onerror="this.src='/PJ1/public/assets/img/no-image.jpg'">
                                <div class="search-item-info">
                                    <h5>${this.highlightKeyword(product.name, keyword)}</h5>
                                    <p class="price">${parseInt(product.sale_price || product.price || 0).toLocaleString('vi-VN')} VNĐ</p>
                                </div>
                            </a>
                        `).join('')}
                    </div>
                </div>`;
        } else {
            html += `
                <div class="search-section">
                    <p class="no-results">Không tìm thấy sản phẩm nào phù hợp</p>
                </div>`;
        }
        
        // Hiển thị bài viết
        if (data.posts && data.posts.length > 0) {
            html += `
                <div class="search-section">
                    <h4><i class="far fa-newspaper"></i> Bài viết (${data.posts.length})</h4>
                    <div class="search-posts">
                        ${data.posts.map(post => `
                            <a href="/PJ1/public/news/${post.slug}" class="search-item">
                                <img src="/PJ1/public/assets/img/${post.image || 'default-news.jpg'}" alt="${post.title}" onerror="this.src='/PJ1/public/assets/img/default-news.jpg'">
                                <div class="search-item-info">
                                    <h5>${this.highlightKeyword(post.title, keyword)}</h5>
                                    <p class="excerpt">${post.excerpt || ''}</p>
                                </div>
                            </a>
                        `).join('')}
                    </div>
                </div>`;
        }
        
        // Kiểm tra nếu không có kết quả
        if ((!data.products || data.products.length === 0) && 
            (!data.posts || data.posts.length === 0)) {
            html += `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <p>Không tìm thấy kết quả phù hợp với "${keyword}"</p>
                </div>`;
        }
        
        html += `
                </div>
                <div class="search-results-footer">
                    <a href="/PJ1/public/search?q=${encodeURIComponent(keyword)}" class="view-all-results">
                        Xem tất cả kết quả cho "${keyword}" <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>`;
            
        this.searchResults.innerHTML = html;
        this.searchResults.classList.add('active');
    }
    
    highlightKeyword(text, keyword) {
        if (!text || !keyword) return text;
        const regex = new RegExp(`(${keyword})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }
    
    createResultsContainer() {
        const container = document.createElement('div');
        container.id = 'global-search-results';
        document.body.appendChild(container);
        this.searchResults = container;
        
        // Đóng kết quả khi click bên ngoài
        document.addEventListener('click', (e) => {
            if (!this.searchResults.contains(e.target) && 
                e.target !== this.searchInput && 
                e.target !== this.searchButton) {
                this.closeResults();
            }
        });
    }
    
    clearResults() {
        if (this.searchResults) {
            this.searchResults.innerHTML = '';
            this.searchResults.classList.remove('active');
        }
    }
    
    closeResults() {
        this.clearResults();
        if (this.searchInput) this.searchInput.blur();
    }
    
    showError(message) {
        if (!this.searchResults) return;
        
        this.searchResults.innerHTML = `
            <div class="search-error">
                <i class="fas fa-exclamation-circle"></i>
                <div class="error-content">
                    <h4>Đã xảy ra lỗi</h4>
                    <p>${message}</p>
                    <button class="retry-btn">
                        <i class="fas fa-sync-alt"></i> Thử lại
                    </button>
                </div>
            </div>`;
            
        this.searchResults.style.display = 'block';
        
        // Thêm sự kiện cho nút thử lại
        const retryBtn = this.searchResults.querySelector('.retry-btn');
        if (retryBtn) {
            retryBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.handleSearch(e);
            });
        }
        
        // Ẩn kết quả sau 5 giây nếu không tương tác
        if (this.clearResultsTimeout) clearTimeout(this.clearResultsTimeout);
        this.clearResultsTimeout = setTimeout(() => {
            if (this.searchResults && !this.searchResults.matches(':hover')) {
                this.clearResults();
            }
        }, 5000);
    }

}

// Chỉ export class GlobalSearch, không tự động khởi tạo
// Việc khởi tạo sẽ được thực hiện trong file home.php
