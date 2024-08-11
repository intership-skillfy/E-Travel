import { Component, OnInit, AfterViewInit, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { PriceModalComponent } from '../price-modal/price-modal.component';
import { environment } from 'src/environments/environment';
import { OffersService} from 'src/app/services/offer/offers.service';

declare var $: any;

@Component({
  selector: 'app-omra-details',
  templateUrl: './omra-details.component.html',
  styleUrls: ['./omra-details.component.scss']
})
export class OmraDetailsComponent implements OnInit, AfterViewInit {
  form: FormGroup;
  fileNames: string[] = [];
  isLoading = false;
  availableCategories: string[] = ['culture', 'sport', 'sahara', 'montagne', 'summer'];
  public priceList: any[] = [];
  error: any;
  backendUrl = environment.apiUrl;
  currentOfferId: number | null = null;
  currentOfferType: string | null = null; // Variable to store current offer type

  dataTable: any;

  @ViewChild('dataTable', { static: false }) tableElement: ElementRef;

  constructor(
    private offersService: OffersService,
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
      this.currentOfferId = params['id'] ? +params['id'] : null;
      this.currentOfferType = params['type'] || null; // Extract offer type from query params

      if (this.currentOfferId && this.currentOfferType) {
        this.fetchOfferDetails();
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

  fetchOfferDetails() {
    if (this.currentOfferId != null) {
        this.offersService.getOfferById(this.currentOfferId).subscribe({
          next: (data: any) => {
            this.form.patchValue(data);
            this.priceList = data.pricesList || [];
            this.updateDataTable();
          },
          error: (e: any) => {
            console.error('Error fetching offer details:', e);
          }
        });
    }
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
      this.dataTable.clear();
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
      this.dataTable.rows.add(formattedData);
      this.dataTable.draw();
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
        this.updateDataTable();
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
    this.updateDataTable();
  }

  onSave() {
    if (this.form.valid && this.currentOfferId !== null && this.currentOfferType) {
      this.isLoading = true;
      const offerData = this.form.value;

      if (this.currentOfferType === 'excursion') {
        this.offersService.updateOffer(this.currentOfferId, offerData).subscribe({
          next: (response) => {
            console.log('Offer updated successfully:', response);
            this.isLoading = false;
          },
          error: (error) => {
            console.error('Error updating offer:', error);
            this.isLoading = false;
          }
        });
      } else if (this.currentOfferType === 'omra') {
        // Handle 'omra' offer type if needed
      }
      // Add other offer types as needed
    } else {
      console.log('Form is invalid or ID/type is not set');
    }
  }
}
