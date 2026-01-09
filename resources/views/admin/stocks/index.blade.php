@extends('admin.master')
@section('content')
<div class="content-wrapper">
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0">Stock Management</h1>
            </div>
            <div class="col-sm-6">
               <div class="pull-right">
                  <button class="btn btn-primary" data-toggle="modal" data-target="#addStockModal">
                     <i class="fas fa-plus"></i> Add Stock
                  </button>
                  <button class="btn btn-success" data-toggle="modal" data-target="#bulkAddModal">
                     <i class="fas fa-layer-group"></i> Bulk Add
                  </button>
               </div>
            </div>
         </div>
      </div>
   </div>

   <section class="content">
      <div class="container-fluid">
         <!-- Date Filter -->
         <div class="row mb-3">
            <div class="col-md-4">
               <label>Select Date:</label>
               <input type="date" id="filterDate" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
               <label>&nbsp;</label><br>
               <button class="btn btn-info" onclick="loadStockList()">
                  <i class="fas fa-search"></i> Load Stock
               </button>
               <button class="btn btn-warning" onclick="loadDailyReport()">
                  <i class="fas fa-chart-line"></i> Daily Report
               </button>
               <button class="btn btn-primary" onclick="loadBrandsGrouped()">
                  <i class="fas fa-layer-group"></i> Brands & Stock
               </button>
            </div>
            <div class="col-md-4">
               <label>Report Type:</label>
               <select id="reportType" class="form-control" onchange="loadReport()">
                  <option value="">Select Report</option>
                  <option value="datewise">Date-wise Report</option>
                  <option value="daily">Daily Report</option>
                  <option value="weekly">Weekly Report</option>
                  <option value="monthly">Monthly Report</option>
               </select>
            </div>
         </div>

         <!-- Alert Messages -->
         <div id="alertMessage" style="display:none;"></div>

         <!-- Stock List Table -->
         <div class="card">
            <div class="card-header">
               <h3 class="card-title">Stock List - <span id="currentDate">{{ date('Y-m-d') }}</span></h3>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped" id="stockTable">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Brand</th>
                           <th>Category</th>
                           <th>Type</th>
                           <th>Quantity</th>
                           <th>Date</th>
                           <th>Actions</th>
                        </tr>
                     </thead>
                     <tbody id="stockTableBody">
                        <tr>
                           <td colspan="7" class="text-center">Loading...</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>

         <!-- Brands Grouped by Category Section -->
         <div class="card" id="brandsGroupedCard" style="display:none;">
            <div class="card-header">
               <h3 class="card-title">Brands Grouped by Category - Available Stock</h3>
               <div class="card-tools">
                  <button type="button" class="btn btn-tool" onclick="document.getElementById('brandsGroupedCard').style.display='none'">
                     <i class="fas fa-times"></i>
                  </button>
               </div>
            </div>
            <div class="card-body">
               <div id="brandsGroupedContent"></div>
            </div>
         </div>

         <!-- Report Section -->
         <div class="card" id="reportCard" style="display:none;">
            <div class="card-header">
               <h3 class="card-title">Report</h3>
            </div>
            <div class="card-body">
               <div id="reportContent"></div>
            </div>
         </div>
      </div>
   </section>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Add Stock</h5>
            <button type="button" class="close" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="addStockForm">
               <div class="form-group">
                  <label>Brand *</label>
                  <select class="form-control" name="brand" id="addBrand" required onchange="updateCategoryOptions('add')">
                     <option value="">Select Brand</option>
                  </select>
               </div>
               <div class="form-group">
                  <label>Category (Mobile Model)</label>
                  <select class="form-control" name="category" id="addCategory">
                     <option value="">Select Brand First</option>
                  </select>
                  <small class="form-text text-muted">Select a brand to see available mobile models</small>
               </div>
               <div class="form-group">
                  <label>Quantity In (Add Stock)</label>
                  <input type="number" class="form-control" name="quantity_in" min="0" placeholder="Enter quantity to add">
                  <small class="form-text text-muted">Leave empty if no stock to add</small>
               </div>
               <div class="form-group">
                  <label>Notes for In</label>
                  <textarea class="form-control" name="notes_in" rows="1" placeholder="Optional notes for stock in"></textarea>
               </div>
               <div class="form-group">
                  <label>Quantity Out (Sell Stock)</label>
                  <input type="number" class="form-control" name="quantity_out" min="0" placeholder="Enter quantity to sell">
                  <small class="form-text text-muted">Leave empty if no stock to sell</small>
               </div>
               <div class="form-group">
                  <label>Notes for Out</label>
                  <textarea class="form-control" name="notes_out" rows="1" placeholder="Optional notes for stock out"></textarea>
               </div>
               <div class="alert alert-info">
                  <strong>Note:</strong> You can add both In and Out quantities at the same time. At least one quantity must be provided.
               </div>
               <div class="form-group">
                  <label>Date *</label>
                  <input type="date" class="form-control" name="stock_date" value="{{ date('Y-m-d') }}" required>
               </div>
               <div class="form-group">
                  <label>General Notes</label>
                  <textarea class="form-control" name="notes" rows="2" placeholder="General notes (used if specific notes not provided)"></textarea>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="addStock()">Add Stock</button>
         </div>
      </div>
   </div>
</div>

<!-- Bulk Add Modal -->
<div class="modal fade" id="bulkAddModal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Bulk Add Stock</h5>
            <button type="button" class="close" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label>Date *</label>
               <input type="date" class="form-control" id="bulkDate" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="alert alert-info">
               <strong>Note:</strong> You can add both In and Out quantities for each item. Leave empty if not applicable.
            </div>
            <div id="bulkStockItems">
               <div class="row mb-2">
                  <div class="col-md-3"><strong>Brand</strong></div>
                  <div class="col-md-3"><strong>Category</strong></div>
                  <div class="col-md-2"><strong>Qty In</strong></div>
                  <div class="col-md-2"><strong>Qty Out</strong></div>
                  <div class="col-md-2"><strong>Action</strong></div>
               </div>
               <div class="stock-item-row mb-2">
                  <div class="row">
                     <div class="col-md-3">
                        <select class="form-control form-control-sm bulk-brand-select" name="bulk_brand[]" required onchange="updateBulkCategory(this)">
                           <option value="">Select</option>
                        </select>
                     </div>
                     <div class="col-md-3">
                        <select class="form-control form-control-sm bulk-category-select" name="bulk_category[]">
                           <option value="">Select Brand First</option>
                        </select>
                     </div>
                     <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" name="bulk_quantity_in[]" placeholder="0" min="0">
                     </div>
                     <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" name="bulk_quantity_out[]" placeholder="0" min="0">
                     </div>
                     <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeStockRow(this)">
                           <i class="fas fa-times"></i>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <button type="button" class="btn btn-sm btn-success" onclick="addStockRow()">
               <i class="fas fa-plus"></i> Add Row
            </button>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="bulkAddStock()">Add All</button>
         </div>
      </div>
   </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Edit Stock</h5>
            <button type="button" class="close" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="editStockForm">
               <input type="hidden" id="editStockId">
               <div class="form-group">
                  <label>Brand *</label>
                  <select class="form-control" id="editBrand" required onchange="updateCategoryOptions('edit')">
                     <option value="">Select Brand</option>
                  </select>
               </div>
               <div class="form-group">
                  <label>Category (Mobile Model)</label>
                  <select class="form-control" id="editCategory">
                     <option value="">Select Brand First</option>
                  </select>
                  <small class="form-text text-muted">Select a brand to see available mobile models</small>
               </div>
               <div class="form-group">
                  <label>Transaction Type *</label>
                  <select class="form-control" id="editTransactionType" required>
                     <option value="in">In (Add Stock)</option>
                     <option value="out">Out (Sell Stock)</option>
                  </select>
               </div>
               <div class="form-group">
                  <label>Quantity *</label>
                  <input type="number" class="form-control" id="editQuantity" min="1" required>
               </div>
               <div class="form-group">
                  <label>Date *</label>
                  <input type="date" class="form-control" id="editStockDate" required>
               </div>
               <div class="form-group">
                  <label>Notes</label>
                  <textarea class="form-control" id="editNotes" rows="2"></textarea>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="updateStock()">Update Stock</button>
         </div>
      </div>
   </div>
</div>

<script>
const API_BASE = '{{ url("/api") }}';

// Mobile models/categories for each brand - loaded from API
let brandCategories = {};
let brandsList = [];

// Load brands and categories from API on page load
document.addEventListener('DOMContentLoaded', function() {
   loadBrandsAndCategories();
});

// Function to load brands and categories from API
function loadBrandsAndCategories() {
   // Load brands list
   fetch(`${API_BASE}/brands/list`)
      .then(response => response.json())
      .then(data => {
         if (data.data) {
            brandsList = data.data;
            populateBrandDropdowns();
         }
      })
      .catch(error => {
         console.error('Error loading brands:', error);
         showAlert('Error loading brands. Using default brands.', 'warning');
         // Fallback to default brands
         brandsList = [
            {id: 1, name: 'Apple'},
            {id: 2, name: 'Samsung'},
            {id: 3, name: 'OPPO'},
            {id: 4, name: 'vivo'}
         ];
         populateBrandDropdowns();
      });

   // Load brands with categories
   fetch(`${API_BASE}/brands`)
      .then(response => response.json())
      .then(data => {
         if (data.data) {
            brandCategories = data.data;
         }
      })
      .catch(error => {
         console.error('Error loading categories:', error);
         showAlert('Error loading categories. Please refresh the page.', 'warning');
      });
}

// Populate brand dropdowns in all modals
function populateBrandDropdowns() {
   // Function to populate a single select element
   const populateSelect = (select) => {
      if (!select) return;
      
      // Keep the first option (Select Brand) if it exists
      const firstOption = select.querySelector('option[value=""]');
      select.innerHTML = '';
      
      const defaultOption = document.createElement('option');
      defaultOption.value = '';
      defaultOption.textContent = firstOption ? firstOption.textContent : 'Select Brand';
      select.appendChild(defaultOption);

      // Add brands from API
      brandsList.forEach(brand => {
         const option = document.createElement('option');
         option.value = brand.name;
         option.textContent = brand.name;
         select.appendChild(option);
      });
   };

   // Populate existing selects
   const addBrandSelect = document.getElementById('addBrand');
   const editBrandSelect = document.getElementById('editBrand');
   const bulkBrandSelects = document.querySelectorAll('.bulk-brand-select');

   if (addBrandSelect) populateSelect(addBrandSelect);
   if (editBrandSelect) populateSelect(editBrandSelect);
   bulkBrandSelects.forEach(select => populateSelect(select));
}

// Function to populate brand dropdown for a newly added row
function populateNewRowBrandSelect(selectElement) {
   if (!selectElement || !brandsList.length) return;
   
   const defaultOption = document.createElement('option');
   defaultOption.value = '';
   defaultOption.textContent = 'Select';
   selectElement.innerHTML = '';
   selectElement.appendChild(defaultOption);

   brandsList.forEach(brand => {
      const option = document.createElement('option');
      option.value = brand.name;
      option.textContent = brand.name;
      selectElement.appendChild(option);
   });
}

// Update category dropdown based on selected brand
function updateCategoryOptions(formType) {
   const brandSelect = formType === 'edit' ? document.getElementById('editBrand') : document.getElementById('addBrand');
   const categorySelect = formType === 'edit' ? document.getElementById('editCategory') : document.getElementById('addCategory');
   
   const selectedBrand = brandSelect.value;
   categorySelect.innerHTML = '<option value="">Loading...</option>';
   
   if (!selectedBrand) {
      categorySelect.innerHTML = '<option value="">Select Brand First</option>';
      return;
   }

   // Check if categories are already loaded in cache
   if (brandCategories[selectedBrand] && brandCategories[selectedBrand].length > 0) {
      categorySelect.innerHTML = '<option value="">Select Mobile Model</option>';
      brandCategories[selectedBrand].forEach(model => {
         const option = document.createElement('option');
         option.value = model;
         option.textContent = model;
         categorySelect.appendChild(option);
      });
   } else {
      // Fetch categories from API
      fetch(`${API_BASE}/categories?brand_name=${encodeURIComponent(selectedBrand)}`)
         .then(response => response.json())
         .then(data => {
            categorySelect.innerHTML = '<option value="">Select Mobile Model</option>';
            
            if (data.data && data.data.length > 0) {
               // Cache the categories
               brandCategories[selectedBrand] = data.data.map(cat => cat.name);
               
               data.data.forEach(category => {
                  const option = document.createElement('option');
                  option.value = category.name;
                  option.textContent = category.name;
                  categorySelect.appendChild(option);
               });
            } else {
               categorySelect.innerHTML = '<option value="">No models available</option>';
            }
         })
         .catch(error => {
            console.error('Error loading categories:', error);
            categorySelect.innerHTML = '<option value="">Error loading categories</option>';
         });
   }
}

// Load stock list
function loadStockList() {
   const date = document.getElementById('filterDate').value;
   document.getElementById('currentDate').textContent = date;
   document.getElementById('reportCard').style.display = 'none';
   document.getElementById('brandsGroupedCard').style.display = 'none';
   document.getElementById('stockTable').closest('.card').style.display = 'block';
   
   fetch(`${API_BASE}/stock-list?date=${date}`)
      .then(response => response.json())
      .then(data => {
         const tbody = document.getElementById('stockTableBody');
         if (data.data && data.data.length > 0) {
            tbody.innerHTML = data.data.map(stock => {
               const typeBadge = stock.transaction_type === 'in' 
                  ? '<span class="badge badge-success">IN</span>' 
                  : '<span class="badge badge-danger">OUT</span>';
               const category = stock.category || '-';
               const notes = (stock.notes || '').replace(/'/g, "\\'");
               
               return `
               <tr>
                  <td>${stock.id}</td>
                  <td>${stock.brand}</td>
                  <td>${category}</td>
                  <td>${typeBadge}</td>
                  <td><strong>${stock.quantity}</strong></td>
                  <td>${stock.stock_date}</td>
                  <td>
                     <button class="btn btn-sm btn-info" onclick="editStock(${stock.id}, '${stock.brand}', '${category}', '${stock.transaction_type}', ${stock.quantity}, '${stock.stock_date}', '${notes}')">
                        <i class="fas fa-edit"></i>
                     </button>
                     <button class="btn btn-sm btn-danger" onclick="deleteStock(${stock.id})">
                        <i class="fas fa-trash"></i>
                     </button>
                  </td>
               </tr>
            `;
            }).join('');
         } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">No stock found for this date</td></tr>';
         }
      })
      .catch(error => {
         showAlert('Error loading stock: ' + error.message, 'danger');
      });
}

// Add single stock
function addStock() {
   const form = document.getElementById('addStockForm');
   const formData = new FormData(form);
   const data = Object.fromEntries(formData);
   
   // Convert empty strings to null and parse numbers
   if (data.quantity_in === '' || data.quantity_in === '0') {
      delete data.quantity_in;
   } else if (data.quantity_in) {
      data.quantity_in = parseInt(data.quantity_in);
   }
   
   if (data.quantity_out === '' || data.quantity_out === '0') {
      delete data.quantity_out;
   } else if (data.quantity_out) {
      data.quantity_out = parseInt(data.quantity_out);
   }
   
   // Remove empty notes
   if (!data.notes_in || data.notes_in.trim() === '') delete data.notes_in;
   if (!data.notes_out || data.notes_out.trim() === '') delete data.notes_out;
   if (!data.notes || data.notes.trim() === '') delete data.notes;
   
   // Validate at least one quantity is provided
   if (!data.quantity_in && !data.quantity_out) {
      showAlert('Please provide at least one quantity (In or Out)', 'warning');
      return;
   }
   
   fetch(`${API_BASE}/stock-add`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
   })
   .then(response => response.json())
   .then(data => {
      if (data.message) {
         showAlert(data.message, 'success');
         $('#addStockModal').modal('hide');
         form.reset();
         loadStockList();
      } else {
         showAlert(data.error || 'Error adding stock', 'danger');
      }
   })
   .catch(error => {
      showAlert('Error: ' + error.message, 'danger');
   });
}

// Bulk add stock
function bulkAddStock() {
   const date = document.getElementById('bulkDate').value;
   const brands = document.getElementsByName('bulk_brand[]');
   const categories = document.getElementsByName('bulk_category[]');
   const quantitiesIn = document.getElementsByName('bulk_quantity_in[]');
   const quantitiesOut = document.getElementsByName('bulk_quantity_out[]');
   
   const stocks = [];
   for (let i = 0; i < brands.length; i++) {
      if (brands[i].value && (quantitiesIn[i].value || quantitiesOut[i].value)) {
         const stockData = {
            brand: brands[i].value,
            category: categories[i] ? categories[i].value : null,
         };
         
         // Add quantity_in if provided
         if (quantitiesIn[i].value && parseInt(quantitiesIn[i].value) > 0) {
            stockData.quantity_in = parseInt(quantitiesIn[i].value);
         }
         
         // Add quantity_out if provided
         if (quantitiesOut[i].value && parseInt(quantitiesOut[i].value) > 0) {
            stockData.quantity_out = parseInt(quantitiesOut[i].value);
         }
         
         stocks.push(stockData);
      }
   }
   
   if (stocks.length === 0) {
      showAlert('Please add at least one stock item with quantity', 'warning');
      return;
   }
   
   // Use stock-bulk-add but with new format
   // We'll need to flatten the array to include both in and out as separate entries
   const flatStocks = [];
   stocks.forEach(stock => {
      if (stock.quantity_in) {
         flatStocks.push({
            brand: stock.brand,
            category: stock.category,
            size: stock.size,
            color: stock.color,
            quantity: stock.quantity_in,
            transaction_type: 'in'
         });
      }
      if (stock.quantity_out) {
         flatStocks.push({
            brand: stock.brand,
            category: stock.category,
            size: stock.size,
            color: stock.color,
            quantity: stock.quantity_out,
            transaction_type: 'out'
         });
      }
   });
   
   if (flatStocks.length === 0) {
      showAlert('Please provide at least one quantity (In or Out)', 'warning');
      return;
   }
   
   fetch(`${API_BASE}/stock-bulk-add`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
      },
      body: JSON.stringify({
         stock_date: date,
         stocks: flatStocks
      })
   })
   .then(response => response.json())
   .then(data => {
      if (data.message) {
         showAlert(data.message, 'success');
         $('#bulkAddModal').modal('hide');
         document.getElementById('bulkStockItems').innerHTML = `
            <div class="row mb-2">
               <div class="col-md-3"><strong>Brand</strong></div>
               <div class="col-md-3"><strong>Category</strong></div>
               <div class="col-md-2"><strong>Qty In</strong></div>
               <div class="col-md-2"><strong>Qty Out</strong></div>
               <div class="col-md-2"><strong>Action</strong></div>
            </div>
            <div class="stock-item-row mb-2">
               <div class="row">
                  <div class="col-md-3">
                     <select class="form-control form-control-sm bulk-brand-select" name="bulk_brand[]" required onchange="updateBulkCategory(this)">
                        <option value="">Select</option>
                        <option value="Apple">Apple</option>
                        <option value="Samsung">Samsung</option>
                        <option value="OPPO">OPPO</option>
                        <option value="vivo">vivo</option>
                     </select>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control form-control-sm bulk-category-select" name="bulk_category[]">
                        <option value="">Select Brand First</option>
                     </select>
                  </div>
                  <div class="col-md-2">
                     <input type="number" class="form-control form-control-sm" name="bulk_quantity_in[]" placeholder="0" min="0">
                  </div>
                  <div class="col-md-2">
                     <input type="number" class="form-control form-control-sm" name="bulk_quantity_out[]" placeholder="0" min="0">
                  </div>
                  <div class="col-md-2">
                     <button type="button" class="btn btn-sm btn-danger" onclick="removeStockRow(this)">
                        <i class="fas fa-times"></i>
                     </button>
                  </div>
               </div>
            </div>
         `;
         loadStockList();
      } else {
         showAlert(data.error || 'Error adding stocks', 'danger');
      }
   })
   .catch(error => {
      showAlert('Error: ' + error.message, 'danger');
   });
}

// Update bulk category dropdown
function updateBulkCategory(brandSelect) {
   const row = brandSelect.closest('.stock-item-row');
   const categorySelect = row.querySelector('.bulk-category-select');
   const selectedBrand = brandSelect.value;
   
   categorySelect.innerHTML = '<option value="">Loading...</option>';
   
   if (!selectedBrand) {
      categorySelect.innerHTML = '<option value="">Select Brand First</option>';
      return;
   }

   // Check if categories are already loaded in cache
   if (brandCategories[selectedBrand] && brandCategories[selectedBrand].length > 0) {
      categorySelect.innerHTML = '<option value="">Select Mobile Model</option>';
      brandCategories[selectedBrand].forEach(model => {
         const option = document.createElement('option');
         option.value = model;
         option.textContent = model;
         categorySelect.appendChild(option);
      });
   } else {
      // Fetch categories from API
      fetch(`${API_BASE}/categories?brand_name=${encodeURIComponent(selectedBrand)}`)
         .then(response => response.json())
         .then(data => {
            categorySelect.innerHTML = '<option value="">Select Mobile Model</option>';
            
            if (data.data && data.data.length > 0) {
               // Cache the categories
               brandCategories[selectedBrand] = data.data.map(cat => cat.name);
               
               data.data.forEach(category => {
                  const option = document.createElement('option');
                  option.value = category.name;
                  option.textContent = category.name;
                  categorySelect.appendChild(option);
               });
            } else {
               categorySelect.innerHTML = '<option value="">No models available</option>';
            }
         })
         .catch(error => {
            console.error('Error loading categories:', error);
            categorySelect.innerHTML = '<option value="">Error loading categories</option>';
         });
   }
}

// Add stock row in bulk modal
function addStockRow() {
   const container = document.getElementById('bulkStockItems');
   const newRow = document.createElement('div');
   newRow.className = 'stock-item-row mb-2';
   newRow.innerHTML = `
      <div class="row">
         <div class="col-md-3">
            <select class="form-control form-control-sm bulk-brand-select" name="bulk_brand[]" required onchange="updateBulkCategory(this)">
               <option value="">Select</option>
            </select>
         </div>
         <div class="col-md-3">
            <select class="form-control form-control-sm bulk-category-select" name="bulk_category[]">
               <option value="">Select Brand First</option>
            </select>
         </div>
         <div class="col-md-2">
            <input type="number" class="form-control form-control-sm" name="bulk_quantity_in[]" placeholder="0" min="0">
         </div>
         <div class="col-md-2">
            <input type="number" class="form-control form-control-sm" name="bulk_quantity_out[]" placeholder="0" min="0">
         </div>
         <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeStockRow(this)">
               <i class="fas fa-times"></i>
            </button>
         </div>
      </div>
   `;
   container.appendChild(newRow);
   
   // Populate the brand dropdown for the new row
   const brandSelect = newRow.querySelector('.bulk-brand-select');
   if (brandSelect) {
      populateNewRowBrandSelect(brandSelect);
   }
}

// Remove stock row
function removeStockRow(btn) {
   const rows = document.getElementsByClassName('stock-item-row');
   if (rows.length > 1) {
      btn.closest('.stock-item-row').remove();
   } else {
      showAlert('At least one row is required', 'warning');
   }
}

// Edit stock
function editStock(id, brand, category, transactionType, quantity, date, notes) {
   document.getElementById('editStockId').value = id;
   document.getElementById('editBrand').value = brand;
   
   const categorySelect = document.getElementById('editCategory');
   const selectedCategory = category || '';
   
   // Update category options first, then set the value
   if (brandCategories[brand] && brandCategories[brand].length > 0) {
      // Categories already cached, populate immediately
      updateCategoryOptions('edit');
      setTimeout(() => {
         categorySelect.value = selectedCategory;
         // If category is not in the list, add it as an option
         if (selectedCategory && !Array.from(categorySelect.options).some(opt => opt.value === selectedCategory)) {
            const option = document.createElement('option');
            option.value = selectedCategory;
            option.textContent = selectedCategory;
            categorySelect.appendChild(option);
            categorySelect.value = selectedCategory;
         }
      }, 50);
   } else {
      // Need to fetch from API, wait for it to complete
      updateCategoryOptions('edit');
      
      // Wait for categories to load (check every 100ms, max 2 seconds)
      let attempts = 0;
      const checkInterval = setInterval(() => {
         attempts++;
         if (categorySelect.innerHTML !== '<option value="">Loading...</option>' || attempts > 20) {
            clearInterval(checkInterval);
            categorySelect.value = selectedCategory;
            // If category is not in the list, add it as an option
            if (selectedCategory && !Array.from(categorySelect.options).some(opt => opt.value === selectedCategory)) {
               const option = document.createElement('option');
               option.value = selectedCategory;
               option.textContent = selectedCategory;
               categorySelect.appendChild(option);
               categorySelect.value = selectedCategory;
            }
         }
      }, 100);
   }
   
   document.getElementById('editTransactionType').value = transactionType;
   document.getElementById('editQuantity').value = quantity;
   document.getElementById('editStockDate').value = date;
   document.getElementById('editNotes').value = notes || '';
   $('#editStockModal').modal('show');
}

// Update stock - creates new entry with date-wise data
function updateStock() {
   const id = document.getElementById('editStockId').value;
   const brand = document.getElementById('editBrand').value;
   const category = document.getElementById('editCategory').value;
   const transactionType = document.getElementById('editTransactionType').value;
   const quantity = parseInt(document.getElementById('editQuantity').value);
   const stockDate = document.getElementById('editStockDate').value;
   const notes = document.getElementById('editNotes').value;
   
   const data = {
      brand: brand,
      category: category || null,
      transaction_type: transactionType,
      quantity: quantity,
      stock_date: stockDate,
      notes: notes
   };
   
   fetch(`${API_BASE}/stock-update/${id}`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
   })
   .then(response => response.json())
   .then(data => {
      if (data.message) {
         showAlert(data.message, 'success');
         $('#editStockModal').modal('hide');
         loadStockList();
      } else {
         showAlert(data.error || 'Error updating stock', 'danger');
      }
   })
   .catch(error => {
      showAlert('Error: ' + error.message, 'danger');
   });
}

// Delete stock
function deleteStock(id) {
   if (confirm('Are you sure you want to delete this stock?')) {
      fetch(`${API_BASE}/stock-delete/${id}`)
         .then(response => response.json())
         .then(data => {
            if (data.message) {
               showAlert(data.message, 'success');
               loadStockList();
            } else {
               showAlert(data.error || 'Error deleting stock', 'danger');
            }
         })
         .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
         });
   }
}

// Load daily report
function loadDailyReport() {
   const date = document.getElementById('filterDate').value;
   document.getElementById('reportCard').style.display = 'block';
   
   fetch(`${API_BASE}/stock-daily-report?date=${date}`)
      .then(response => response.json())
      .then(data => {
         if (data.data && data.data.length > 0) {
            let html = `<h5>Daily Report for ${date}</h5>`;
            html += `<p><strong>Previous Date:</strong> ${data.previous_date}</p>`;
            html += `<table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th>Brand</th>
                     <th>Size</th>
                     <th>Color</th>
                     <th>Previous Qty</th>
                     <th>Current Qty</th>
                     <th>Change</th>
                  </tr>
               </thead>
               <tbody>`;
            
            data.data.forEach(item => {
               const changeClass = item.change_type === 'plus' ? 'text-success' : 
                                  item.change_type === 'minus' ? 'text-danger' : 'text-muted';
               html += `
                  <tr>
                     <td>${item.brand}</td>
                     <td>${item.size}</td>
                     <td>${item.color}</td>
                     <td>${item.previous_quantity}</td>
                     <td><strong>${item.quantity}</strong></td>
                     <td class="${changeClass}"><strong>${item.change_text}</strong></td>
                  </tr>
               `;
            });
            
            html += `</tbody></table>`;
            document.getElementById('reportContent').innerHTML = html;
         } else {
            document.getElementById('reportContent').innerHTML = '<p>No data available for this date</p>';
         }
      })
      .catch(error => {
         showAlert('Error loading report: ' + error.message, 'danger');
      });
}

// Load date-wise report
function loadDateWiseReport() {
   const date = document.getElementById('filterDate').value;
   document.getElementById('reportCard').style.display = 'block';
   
   fetch(`${API_BASE}/stock-date-report?date=${date}`)
      .then(response => response.json())
      .then(data => {
         if (data.data && data.data.length > 0) {
            let html = `<h5>Date-wise Report for ${date}</h5>`;
            html += `<table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th>Brand</th>
                     <th>Category</th>
                     <th>Add New</th>
                     <th>Minus</th>
                     <th>Remaining</th>
                     <th>Previous Qty</th>
                     <th>Notes</th>
                  </tr>
               </thead>
               <tbody>`;
            
            data.data.forEach(item => {
               html += `
                  <tr>
                     <td>${item.brand}</td>
                     <td>${item.category || '-'}</td>
                     <td class="text-success"><strong>${item.add_new}</strong></td>
                     <td class="text-danger"><strong>${item.minus}</strong></td>
                     <td class="text-primary"><strong>${item.remaining}</strong></td>
                     <td>${item.previous_quantity}</td>
                     <td>${item.notes || '-'}</td>
                  </tr>
               `;
            });
            
            html += `</tbody></table>`;
            document.getElementById('reportContent').innerHTML = html;
         } else {
            document.getElementById('reportContent').innerHTML = '<p>No data available for this date</p>';
         }
      })
      .catch(error => {
         showAlert('Error loading report: ' + error.message, 'danger');
      });
}

// Load report based on type
function loadReport() {
   const type = document.getElementById('reportType').value;
   if (!type) return;
   
   document.getElementById('reportCard').style.display = 'block';
   const date = document.getElementById('filterDate').value;
   
   if (type === 'datewise') {
      loadDateWiseReport();
      return;
   }
   
   let url = '';
   if (type === 'daily') {
      url = `${API_BASE}/stock-daily-report?date=${date}`;
   } else if (type === 'weekly') {
      const weekStart = new Date(date);
      weekStart.setDate(weekStart.getDate() - weekStart.getDay() + 1);
      url = `${API_BASE}/stock-weekly-report?week_start=${weekStart.toISOString().split('T')[0]}`;
   } else if (type === 'monthly') {
      const month = date.substring(0, 7);
      url = `${API_BASE}/stock-monthly-report?month=${month}`;
   }
   
   fetch(url)
      .then(response => response.json())
      .then(data => {
         let html = `<h5>${type.charAt(0).toUpperCase() + type.slice(1)} Report</h5>`;
         
         if (type === 'daily') {
            html += renderDailyReport(data);
         } else if (type === 'weekly') {
            html += renderWeeklyReport(data);
         } else if (type === 'monthly') {
            html += renderMonthlyReport(data);
         }
         
         document.getElementById('reportContent').innerHTML = html;
      })
      .catch(error => {
         showAlert('Error loading report: ' + error.message, 'danger');
      });
}

function renderDailyReport(data) {
   if (!data.data || data.data.length === 0) return '<p>No data available</p>';
   
   let html = `<p><strong>Date:</strong> ${data.date} | <strong>Previous:</strong> ${data.previous_date}</p>`;
   html += `<table class="table table-bordered table-striped">
      <thead><tr><th>Brand</th><th>Category</th><th>Previous</th><th>Current</th><th>Change</th></tr></thead>
      <tbody>`;
   
   data.data.forEach(item => {
      const changeClass = item.change_type === 'plus' ? 'text-success' : 
                         item.change_type === 'minus' ? 'text-danger' : 'text-muted';
      html += `<tr>
         <td>${item.brand}</td>
         <td>${item.category || '-'}</td>
         <td>${item.previous_quantity}</td>
         <td><strong>${item.quantity}</strong></td>
         <td class="${changeClass}"><strong>${item.change_text}</strong></td>
      </tr>`;
   });
   
   html += `</tbody></table>`;
   return html;
}

function renderWeeklyReport(data) {
   if (!data.data) return '<p>No data available</p>';
   
   let html = `<p><strong>Week:</strong> ${data.week_start} to ${data.week_end}</p>`;
   Object.keys(data.data).forEach(date => {
      html += `<h6>${date}</h6>`;
      if (data.data[date].length > 0) {
         html += `<table class="table table-sm table-bordered mb-3">
            <thead><tr><th>Brand</th><th>Category</th><th>Qty</th><th>Change</th></tr></thead>
            <tbody>`;
         data.data[date].forEach(item => {
            const changeClass = item.change_type === 'plus' ? 'text-success' : 
                               item.change_type === 'minus' ? 'text-danger' : 'text-muted';
            html += `<tr>
               <td>${item.brand}</td>
               <td>${item.category || '-'}</td>
               <td>${item.quantity}</td>
               <td class="${changeClass}">${item.change_text}</td>
            </tr>`;
         });
         html += `</tbody></table>`;
      }
   });
   return html;
}

function renderMonthlyReport(data) {
   if (!data.data) return '<p>No data available</p>';
   
   let html = `<p><strong>Month:</strong> ${data.month} (${data.month_start} to ${data.month_end})</p>`;
   Object.keys(data.data).forEach(date => {
      if (data.data[date].length > 0) {
         html += `<h6>${date}</h6>`;
         html += `<table class="table table-sm table-bordered mb-3">
            <thead><tr><th>Brand</th><th>Category</th><th>Qty</th><th>Change</th></tr></thead>
            <tbody>`;
         data.data[date].forEach(item => {
            const changeClass = item.change_type === 'plus' ? 'text-success' : 
                               item.change_type === 'minus' ? 'text-danger' : 'text-muted';
            html += `<tr>
               <td>${item.brand}</td>
               <td>${item.category || '-'}</td>
               <td>${item.quantity}</td>
               <td class="${changeClass}">${item.change_text}</td>
            </tr>`;
         });
         html += `</tbody></table>`;
      }
   });
   return html;
}

// Show alert message
function showAlert(message, type) {
   const alertDiv = document.getElementById('alertMessage');
   alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
   alertDiv.innerHTML = `
      ${message}
      <button type="button" class="close" onclick="this.parentElement.style.display='none'">
         <span>&times;</span>
      </button>
   `;
   alertDiv.style.display = 'block';
   setTimeout(() => {
      alertDiv.style.display = 'none';
   }, 5000);
}

// Load brands grouped by category with available stock
function loadBrandsGrouped() {
   document.getElementById('brandsGroupedCard').style.display = 'block';
   document.getElementById('reportCard').style.display = 'none';
   document.getElementById('stockTable').closest('.card').style.display = 'none';
   
   const contentDiv = document.getElementById('brandsGroupedContent');
   contentDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
   
   fetch(`{{ url('/admin/stocks/brands-grouped') }}`)
      .then(response => response.json())
      .then(data => {
         if (data.data && data.data.length > 0) {
            let html = '<div class="row">';
            
            data.data.forEach(brand => {
               const stockBadgeClass = brand.total_stock > 50 ? 'badge-success' : 
                                      brand.total_stock > 20 ? 'badge-info' : 
                                      brand.total_stock > 10 ? 'badge-warning' : 'badge-danger';
               
               html += `
                  <div class="col-md-6 mb-4">
                     <div class="card card-primary card-outline">
                        <div class="card-header">
                           <h3 class="card-title">
                              <i class="fas fa-mobile-alt mr-2"></i>
                              ${brand.brand}
                              <span class="badge ${stockBadgeClass} float-right">Total: ${brand.total_stock}</span>
                           </h3>
                        </div>
                        <div class="card-body">
               `;
               
               if (brand.categories && brand.categories.length > 0) {
                  brand.categories.forEach(category => {
                     const categoryStockClass = category.total_stock > 20 ? 'text-success' : 
                                                category.total_stock > 10 ? 'text-info' : 
                                                category.total_stock > 5 ? 'text-warning' : 'text-danger';
                     
                     html += `
                        <div class="mb-3">
                           <h5 class="mb-2">
                              <i class="fas fa-tag mr-1"></i>
                              ${category.category}
                              <span class="badge badge-secondary float-right">${category.total_stock} units</span>
                           </h5>
                           <div class="table-responsive">
                              <table class="table table-sm table-bordered">
                                 <thead>
                                    <tr>
                                       <th>In</th>
                                       <th>Out</th>
                                       <th class="${categoryStockClass}"><strong>Available</strong></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                     `;
                     
                     category.items.forEach(item => {
                        const itemStockClass = item.current_quantity > 10 ? 'text-success' : 
                                               item.current_quantity > 5 ? 'text-info' : 
                                               item.current_quantity > 0 ? 'text-warning' : 'text-danger';
                        
                        html += `
                           <tr>
                              <td class="text-success">+${item.total_in}</td>
                              <td class="text-danger">-${item.total_out}</td>
                              <td class="${itemStockClass}"><strong>${item.current_quantity}</strong></td>
                           </tr>
                        `;
                     });
                     
                     html += `
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     `;
                  });
               } else {
                  html += '<p class="text-muted">No stock available for this brand</p>';
               }
               
               html += `
                        </div>
                     </div>
                  </div>
               `;
            });
            
            html += '</div>';
            contentDiv.innerHTML = html;
         } else {
            contentDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No stock available. Please add stock first.</div>';
         }
      })
      .catch(error => {
         showAlert('Error loading brands grouped view: ' + error.message, 'danger');
         contentDiv.innerHTML = '<div class="alert alert-danger">Error loading data. Please try again.</div>';
      });
}

// Load stock list on page load
document.addEventListener('DOMContentLoaded', function() {
   loadStockList();
});
</script>
@endsection

