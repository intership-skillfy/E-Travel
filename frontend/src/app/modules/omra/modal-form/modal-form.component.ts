/*import { Component, OnInit, Input } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { FormControl } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { OffersService } from 'src/app/services/offer/offers.service';

interface UploadResponse {
  filePath: string;
}

interface Offer {
  name: string;
  categories: string[];
  destination: string;
  banner: string;
  type?: string; // Add type to the Offer interface
}

@Component({
  selector: 'app-modal-form',
  templateUrl: './modal-form.component.html',
  styleUrls: ['./modal-form.component.scss']
})
export class ModalFormComponent implements OnInit {
  @Input() offerType: string = ''; // Add offerType property with default value

  offer: Offer = {
    name: '',
    categories: [],
    destination: '',
    banner: '',
    type: '' // Initialize type
  };

  availableCategories: string[] = ['culture', 'sport', 'sahara', 'montagne', 'summer']; // Define your options here
  fileName = '';
  fileToUpload: File | null = null;
  iconAvailable = false;
  categoriesControl = new FormControl<string[]>(this.offer.categories);

  constructor(
    public activeModal: NgbActiveModal,
    private http: HttpClient,
    private router: Router,
    private route: ActivatedRoute,
    private OffersService: OffersService // Inject the service
  ) {}

  ngOnInit(): void {
    this.iconAvailable = !!document.querySelector('mat-icon');

    // Set the offer type from query parameters
    this.route.queryParams.subscribe(params => {
      this.offer.type = params['type'] || '';  // Set the offer type from query parameters
    });
  }

  close() {
    this.activeModal.dismiss(); // Close the modal
  }

  onSubmit() {
    const selectedCategories = this.categoriesControl.value || [];
    this.offer.categories = selectedCategories;

    const navigateToDetailsPage = () => {
      const queryParams = {
        name: this.offer.name,
        destination: this.offer.destination,
        //banner: this.offer.banner,
        categories: JSON.stringify(this.offer.categories),
        type: this.offer.type  // Pass the offer type as a query param
      };

      this.router.navigate(['/offers/offer-details'], { queryParams })
        .catch(err => console.error('Navigation error:', err));
    };

    if (this.fileToUpload) {
      const formData = new FormData();
      formData.append("thumbnail", this.fileToUpload);
      this.http.post<UploadResponse>("/api/upload", formData).subscribe({
        next: (response) => {
          this.offer.banner = response.filePath;
          this.OffersService.createOffer(this.offer).subscribe({
            next: () => {
              this.activeModal.close();
              navigateToDetailsPage();
            },
            error: (err) => {
              console.error('Offer creation failed', err);
            }
          });
        },
        error: (err) => {
          console.error('Upload failed', err);
        }
      });
    } else {
      this.OffersService.createOffer(this.offer).subscribe({
        next: () => {
          this.activeModal.close();
          navigateToDetailsPage();
        },
        error: (err) => {
          console.error('Offer creation failed', err);
        }
      });
    }
  }

  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input && input.files && input.files.length) {
      const file = input.files[0];
      this.fileName = file.name;
      this.fileToUpload = file;
    }
  }
}
*/
import { Component, OnInit, Input } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { FormControl } from '@angular/forms';
import { Router } from '@angular/router';

interface UploadResponse {
  filePath: string;
}

interface Offer {
  name: string;
  categories: string[];
  destination: string;
  banner: string;
}

@Component({
  selector: 'app-modal-form',
  templateUrl: './modal-form.component.html',
  styleUrls: ['./modal-form.component.scss']
})
export class ModalFormComponent implements OnInit {
  @Input() offerType: string = 'Offer'; // Input property for the offer type
  offer: Offer = {
    name: '',
    categories: [],
    destination: '',
    banner: ''
  };

  availableCategories: string[] = ['culture', 'sport', 'sahara', 'montagne', 'summer']; // Define your options here
  fileName = '';
  fileToUpload: File | null = null;
  iconAvailable = false;
  categoriesControl = new FormControl<string[]>(this.offer.categories);

  constructor(public activeModal: NgbActiveModal, private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    this.iconAvailable = !!document.querySelector('mat-icon');
  }

  close() {
    this.activeModal.dismiss(); // Close the modal
  }

  onSubmit() {
    const selectedCategories = this.categoriesControl.value || [];
    this.offer.categories = selectedCategories;
  
    const navigateToDetailsPage = () => {
      const queryParams = {
        name: this.offer.name,
        destination: this.offer.destination,
        banner: this.offer.banner,
        categories: JSON.stringify(this.offer.categories),
        type: this.offerType  // Pass the offer type as a query param
      };
  
      this.router.navigate(['/offers/offer-details'], { queryParams })
        .catch(err => console.error('Navigation error:', err));
    };
  
    if (this.fileToUpload) {
      const formData = new FormData();
      formData.append("thumbnail", this.fileToUpload);
      this.http.post<UploadResponse>("/api/", formData).subscribe({
        next: (response) => {
          this.offer.banner = response.filePath;
          this.activeModal.close();
          navigateToDetailsPage();
        },
        error: (err) => {
          console.error('Upload failed', err);
        }
      });
    } else {
      this.activeModal.close();
      navigateToDetailsPage();
    }
  }

  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input && input.files && input.files.length) {
      const file = input.files[0];
      this.fileName = file.name;
      this.fileToUpload = file;
    }
  }
}
