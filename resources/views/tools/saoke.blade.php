<x-layouts.appclient>

    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/x-icon" href="logo.svg">
        <title>Check var quyên góp bão lụt</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-grid.css">
        <link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-alpine.css">
        <script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
    </head>

    <section class="mx-auto w-full max-w-8xl px-5 sm:px-10">
        <div class="rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4 text-slate-800">Check var quyên góp</h1>
            <p class="text-sm text-slate-600 mb-4">
                Dữ liệu được lấy từ file của page: <a href="https://www.facebook.com/thongtinchinhphu/" target="_blank"
                    class="text-blue-500 hover:text-blue-600">Thông Tin Chính Phủ</a> - từ ngày 01/09 - 12/09 năm 2024
            </p>
            <div id="searchContainer" class="mb-6">
                <!-- Select Bank nằm bên trên -->
                <div class="mb-4">
                    <label for="bankSelect" class="block text-sm font-medium text-slate-700 mb-2">Chọn ngân
                        hàng:</label>
                    <select id="bankSelect"
                        class="block w-full md:w-1/3 px-4 py-2 border border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="vcb" selected>VCB</option>
                        <option value="viettinbank">ViettinBank</option>
                    </select>
                </div>
                <!-- Search Input và Button -->
                <div class="flex flex-col md:flex-row">
                    <input type="text" id="searchInput" placeholder="Nhập từ khóa tìm kiếm"
                        class="flex-grow px-4 py-2 border border-slate-300 rounded-t-md md:rounded-l-md md:rounded-t-none focus:ring-blue-500 focus:border-blue-500">
                    <button id="searchButton"
                        class="bg-blue-500 text-white px-6 py-2 rounded-b-md md:rounded-r-md md:rounded-b-none mt-2 md:mt-0">Tìm
                        kiếm</button>
                </div>
            </div>
            <div id="loading" class="hidden text-center text-sky-600 my-4">
                <svg class="animate-spin h-5 w-5 mr-3 inline-block" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Đang tìm kiếm...
            </div>
            <div class="overflow-x-auto mb-4 ag-theme-alpine">
                <div id="dataGrid" class="ag-theme-alpine" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
    </section>

    <script>
        const gridOptions = {
            autoSizeStrategy: {
                type: "fitCellContents",
            },
            columnDefs: [{
                    headerName: 'Ngày GD',
                    field: 'd',
                    width: 150
                },
                {
                    headerName: 'Mã GD',
                    field: 'no'
                },
                {
                    headerName: 'Số tiền',
                    field: 'am',
                    // valueFormatter: params => formatter.format(params.value)
                },
                {
                    headerName: 'Nội dung',
                    field: 'c',
                    flex: 1
                }
            ],
            rowData: [],
            pagination: true,
            paginationPageSize: 20,
            domLayout: 'autoHeight',
            defaultColDef: {
                resizable: true,
                sortable: true,
                filter: true
            }
        };

        const formatter = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        });

        document.addEventListener('DOMContentLoaded', function() {
            const eGridDiv = document.querySelector('#dataGrid');
            const grid = new agGrid.Grid(eGridDiv, gridOptions);

            document.getElementById('searchButton').addEventListener('click', function() {
                const query = document.getElementById('searchInput').value;
                const bank = document.getElementById('bankSelect').value;
                if (query) {
                    searchData(query, bank);
                }
            });

            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.which === 13) {
                    document.getElementById('searchButton').click();
                }
            });
        });

        async function searchData(query, bank) {
            document.getElementById('loading').classList.remove('hidden');

            try {
                const response = await fetch(`{{ route('tools.search.var') }}?query=${query}&bank=${bank}`);
                const data = await response.json();

                // Update the grid data
                gridOptions.api.setGridOption('rowData', data);
                // Apply auto size strategy
                gridOptions.api.sizeColumnsToFit();
                document.getElementById('loading').classList.add('hidden');
            } catch (error) {
                console.error('Error fetching search data:', error);
                document.getElementById('loading').classList.add('hidden');
            }
        }
    </script>

</x-layouts.appclient>
