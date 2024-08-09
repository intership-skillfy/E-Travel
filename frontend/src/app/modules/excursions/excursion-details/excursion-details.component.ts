import { Component, OnInit, AfterViewInit, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { PriceModalComponent } from '../price-modal/price-modal.component';
import { environment } from 'src/environments/environment';
import { ExcursionsService } from 'src/app/services/excursion/excursions.service';


declare var $: any;

@Component({
  selector: 'app-excursion-details',
  templateUrl: './excursion-details.component.html',
  styleUrls: ['./excursion-details.component.scss']
})
export class ExcursionDetailsComponent implements OnInit, AfterViewInit {
  form: FormGroup;
  fileNames: string[] = [];
  isLoading = false;
  availableCategories: string[] = ['culture', 'sport', 'sahara', 'montagne', 'summer']; // Define your options here
  public priceList: any[] = [];
  error: any;
  backendUrl = environment.apiUrl;
  currentOfferId: number | null = null; // Variable to store the current offer ID

  dataTable: any;

  @ViewChild('dataTable', { static: false }) tableElement: ElementRef;

  constructor(
    private excursionsService: ExcursionsService,
    private route: ActivatedRoute,
    private fb: FormBuilder,
    private ngbModal: NgbModal
  ) {
    this.form = this.fb.group({
      name: [''],
      destination: [''],
      banner: [''],
      categories: [[]],
      description: [''],
      detailedDescription: [''],
      pricesList: [[]],
      included: [''],
      notIncluded: [''],
      images: [[]]
    });
  }

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.currentOfferId = params['id'] ? +params['id'] : null; // Convert ID to number
      console.log('ID parameter:', this.currentOfferId);

      if (this.currentOfferId) {
        this.fetchExcursionDetails(this.currentOfferId);
      } else {
        this.form.patchValue({
          name: params['name'] || '',
          destination: params['destination'] || '',
          banner: params['banner'] || '',
          categories: params['categories'] ? JSON.parse(params['categories']) : [],
          pricesList: params['pricesList'] ? JSON.parse(params['pricesList']) : []
        });
        this.priceList = this.form.get('pricesList')?.value || [];
      }
    });
  }
  fetchExcursionDetails(id: number) {
    // Implement the logic to fetch excursion details from the backend using the provided ID
    this.excursionsService.getOfferById(id).subscribe({
      next: (data: { name: any; destination: any; banner: any; categories: any; description: any; detailedDescription: any; included: any; notIncluded: any; images: any; pricesList: any; }) => {
        // Update form and priceList with the fetched data
        this.form.patchValue({
          name: data.name,
          destination: data.destination,
          banner: data.banner,
          categories: data.categories,
          description: data.description,
          detailedDescription: data.detailedDescription,
          included: data.included,
          notIncluded: data.notIncluded,
          images: data.images,
          pricesList: data.pricesList
        });
        this.priceList = this.form.get('pricesList')?.value || [];
        this.updateDataTable(); // Refresh DataTable with fetched data if necessary
      },
      error: (e: any) => {
        console.error('Error fetching excursion details:', e);
        // Handle error (e.g., show error message to the user)
      }
    });
  }

  ngAfterViewInit(): void {
    this.initializeDataTable();
  }

  initializeDataTable(): void {
    if (this.tableElement && this.tableElement.nativeElement) {
      this.dataTable = $(this.tableElement.nativeElement).DataTable({
        data: this.priceList.map(price => [
          price.start_date,
          price.end_date,
          price.hotels,
          price.price,
          `<button type="button" class="btn btn-sm btn-light-primary edit-price-btn">
             <i class="fa-solid fa-pen-to-square"></i>
           </button>
           <button type="button" class="btn btn-sm btn-light-danger delete-price-btn">
             <i class="fa-solid fa-trash"></i>
           </button>`
        ]), 
        columns: [
          { title: 'Start Date' },
          { title: 'End Date' },
          { title: 'Hotels' },
          { title: 'Price' },
          { title: 'Actions', orderable: false }
        ]
      });

      $(this.tableElement.nativeElement).on('click', '.edit-price-btn', (event: any) => {
        const index = $(event.currentTarget).closest('tr').index();
        this.editPrice(index);
      });

      $(this.tableElement.nativeElement).on('click', '.delete-price-btn', (event: any) => {
        const index = $(event.currentTarget).closest('tr').index();
        this.deletePrice(index);
      });
    }
  }

  updateDataTable() {
    if (this.dataTable) {
      this.dataTable.clear(); // Clear existing data
      const formattedData = this.priceList.map(price => [
        price.start_date,
        price.end_date,
        price.hotels,
        price.price,
        `<button type="button" class="btn btn-sm btn-light-primary edit-price-btn">
           <i class="fa-solid fa-pen-to-square"></i>
         </button>
         <button type="button" class="btn btn-sm btn-light-danger delete-price-btn">
           <i class="fa-solid fa-trash"></i>
         </button>`
      ]);
      this.dataTable.rows.add(formattedData); // Add new data
      this.dataTable.draw(); // Redraw the table
    }
  }

  getBannerStyle() {
    const banner = this.form.get('banner')?.value || '';
    return banner ? `url(${banner})` : 'url(/assets/default-banner.jpg)';
  }

  onBannerChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
      const file = input.files[0];
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.form.get('banner')?.setValue(e.target.result);
      };
      reader.readAsDataURL(file);
    }
  }

  onFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      this.fileNames = Array.from(input.files).map(file => file.name);
    }
  }

  openPriceModal(index?: number) {
    const modalRef = this.ngbModal.open(PriceModalComponent);
    if (index !== undefined) {
      modalRef.componentInstance.price = this.priceList[index];
      modalRef.componentInstance.index = index;
    }
    modalRef.result.then(result => {
      if (result) {
        if (result.index !== undefined) {
          this.priceList[result.index] = result.price;
        } else {
          this.priceList.push(result.price);
        }
        this.form.patchValue({
          pricesList: this.priceList
        });
        this.updateDataTable(); // Update DataTable with new data
      }
    }).catch(error => {
      console.log('Modal dismissed with error:', error);
    });
  }

  editPrice(index: number) {
    this.openPriceModal(index);
  }

  deletePrice(index: number) {
    this.priceList.splice(index, 1);
    this.form.patchValue({
      pricesList: this.priceList
    });
    this.updateDataTable(); // Update DataTable with new data
  }

  onSave() {
    if (this.form.valid && this.currentOfferId !== null) {
      this.isLoading = true;
      const offerData = this.form.value;

      this.excursionsService.updateOffer(this.currentOfferId, offerData).subscribe({
        next: (response) => {
          console.log('Offer updated successfully:', response);
          this.isLoading = false;
        },
        error: (error) => {
          console.error('Error updating offer:', error);
          this.isLoading = false;
        }
      });
    } else {
      console.log('Form is invalid or ID is not set');
    }
  }
  
  
}
