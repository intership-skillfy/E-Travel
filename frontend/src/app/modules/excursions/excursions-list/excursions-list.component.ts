import { Component, OnInit, AfterViewInit, ElementRef, ViewChild, ChangeDetectorRef } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ExcursionsService } from 'src/app/services/excursion/excursions.service';
import { environment } from 'src/environments/environment';
import { ModalFormComponent } from '../modal-form/modal-form.component';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmationDialogComponent } from '../confirmation-dialog/confirmation-dialog.component';
import { Router } from '@angular/router';

declare var $: any;

@Component({
  selector: 'app-excursions-list',
  templateUrl: './excursions-list.component.html',
  styleUrls: ['./excursions-list.component.scss'],
})
export class ExcursionsListComponent implements OnInit, AfterViewInit {
  backendUrl = environment.apiUrl;
  public excursions: any[] = [];
  public filteredExcursions: any[] = [];
  error: any;
  offerType: string = 'Excursion'; // Default type
  dataTable: any; // DataTable instance

  @ViewChild('dataTable', { static: false }) tableElement: ElementRef;

  constructor(
    private router: Router,
    private excursionsService: ExcursionsService,
    private ngbModal: NgbModal,
    private dialog: MatDialog,
    private cdr: ChangeDetectorRef // Added ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.excursionsService.getOffersByType('all').subscribe({
      next: (data) => {
        console.log('Received data:', data);
        this.excursions = data;
        this.filterExcursions();
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

  filterExcursions(): void {
    console.log('Filtering for type:', this.offerType);
    this.filteredExcursions = this.excursions.filter((offre: { type: string }) => {
      console.log('Comparing:', offre.type, 'with', this.offerType);
      return offre.type.toLowerCase() === this.offerType.toLowerCase();
    });
    console.log('Filtered excursions:', this.filteredExcursions);
  }

  initializeDataTable(): void {
    console.log('Initializing DataTable with', this.filteredExcursions); // Log filtered excursions
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

  openExcursionModal() {
    const modalRef = this.ngbModal.open(ModalFormComponent);
    modalRef.result.then((result) => {
      if (result) {
        this.excursions.push(result);
        this.filterExcursions(); // Filter again after adding a new excursion
      }
    }).catch((error) => {
      console.log('Modal dismissed with error:', error);
    });
  }

  openConfirmDialog(excursion: { name: any; id: number; }): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      width: '250px',
      data: { fileName: excursion.name }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result === true) {
        this.deleteExcursion(excursion.id);
      }
    });
  }

  deleteExcursion(excursionId: number) {
    this.excursionsService.deleteExcursion(excursionId).subscribe({
      next: () => {
        this.excursions = this.excursions.filter(excursion => excursion.id !== excursionId);
        this.filterExcursions();
      },
      error: (e) => {
        console.error('Error deleting excursion:', e);
        alert('Failed to delete the excursion. Please try again.');
      }
    });
  }
}
