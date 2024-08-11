import { Component, OnInit, AfterViewInit, ElementRef, ViewChild, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { OffersService } from 'src/app/services/offer/offers.service';
import { environment } from 'src/environments/environment';
import { ModalFormComponent } from '../modal-form/modal-form.component';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmationDialogComponent } from '../confirmation-dialog/confirmation-dialog.component';
import { Router, ActivatedRoute } from '@angular/router';

declare var $: any;

@Component({
  selector: 'app-omra-list',
  templateUrl: './omra-list.component.html',
  styleUrls: ['./omra-list.component.scss']
})
export class OmraListComponent implements OnInit, AfterViewInit {
  backendUrl = environment.apiUrl;
  public offers: any[] = [];
  public filteredOffers: any[] = [];
  error: any;
  offerType: string = ''; // This will be set dynamically
  dataTable: any; // DataTable instance

  @ViewChild('dataTable', { static: false }) tableElement: ElementRef;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private offersService: OffersService,
    private ngbModal: NgbModal,
    private dialog: MatDialog,
    private cdr: ChangeDetectorRef // Added ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    // Get the offer type from queryParams
    this.offerType = this.route.snapshot.queryParamMap.get('type') || 'omra'; // Set a default type if not provided
  
    this.offersService.getOffersByType(this.offerType).subscribe({
      next: (data) => {
        console.log('Received data:', data); // Debugging log
        this.offers = data;
        this.filterOffers();
        this.cdr.detectChanges(); // Ensure change detection
        this.initializeDataTable(); // Initialize DataTable after change detection
      },
      error: (e) => {
        this.error = e;
        console.error('Error fetching data:', e);
      }
    });
  }
  

  ngAfterViewInit(): void {
    // Do not initialize DataTable here if the data might not be loaded yet
  }

  filterOffers(): void {
    console.log('Filtering for type:', this.offerType);
    this.filteredOffers = this.offers.filter((offer: { type: string }) => {
      console.log('Comparing:', offer.type, 'with', this.offerType);
      return offer.type.toLowerCase() === this.offerType.toLowerCase();
    });
    console.log('Filtered offers:', this.filteredOffers);
  }

  initializeDataTable(): void {
    console.log('Initializing DataTable with', this.filteredOffers); // Log filtered offers
    if (this.tableElement && this.tableElement.nativeElement) {
      if ($.fn.DataTable.isDataTable(this.tableElement.nativeElement)) {
        $(this.tableElement.nativeElement).DataTable().destroy();
      }
      $(this.tableElement.nativeElement).DataTable();
    }
  }
  
  viewDetails(id: number) {
    this.router.navigate(['/offers/offer-details'], { queryParams: { id } });
  }

  openOfferModal() {
    const modalRef = this.ngbModal.open(ModalFormComponent);
    modalRef.result.then((result) => {
      if (result) {
        this.offers.push(result);
        this.filterOffers(); // Filter again after adding a new offer
      }
    }).catch((error) => {
      console.log('Modal dismissed with error:', error);
    });
  }

  openConfirmDialog(offer: { name: any; id: number; }): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      width: '250px',
      data: { fileName: offer.name }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result === true) {
        this.deleteOffer(offer.id);
      }
    });
  }

  deleteOffer(offerId: number) {
    this.offersService.deleteExcursion(offerId).subscribe({
      next: () => {
        this.offers = this.offers.filter(offer => offer.id !== offerId);
        this.filterOffers();
      },
      error: (e) => {
        console.error('Error deleting offer:', e);
        alert('Failed to delete the offer. Please try again.');
      }
    });
  }
}
