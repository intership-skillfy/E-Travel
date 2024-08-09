import { Component, OnInit, OnDestroy } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Subscription, Observable } from 'rxjs';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { ConfirmPasswordValidator } from './confirm-password.validator';
import { first } from 'rxjs/operators';

@Component({
  selector: 'app-registration',
  templateUrl: './clientRegistration.component.html',
  styleUrls: ['./clientRegistration.component.scss'],
})
export class ClientRegistrationComponent implements OnInit, OnDestroy {
  registrationForm: FormGroup;
  hasError: boolean = false;
  isLoading$: Observable<boolean>;
  private unsubscribe: Subscription[] = [];
  fileToUpload: File | null = null;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.isLoading$ = this.authService.isLoading$;
    if (this.authService.currentUserValue) {
      this.router.navigate(['/']);
    }
  }

  ngOnInit(): void {
    this.initForm();
  }

  get f() {
    return this.registrationForm.controls;
  }

  initForm() {
    this.registrationForm = this.fb.group(
      {
        name: [
          '',
          [
            Validators.required,
            Validators.minLength(3),
            Validators.maxLength(100),
          ],
        ],
        email: [
          '',
          [
            Validators.required,
            Validators.email,
            Validators.minLength(3),
            Validators.maxLength(320),
          ],
        ],
        password: [
          '',
          [
            Validators.required,
            Validators.minLength(3),
            Validators.maxLength(100),
          ],
        ],
        cPassword: [
          '',
          [
            Validators.required,
            Validators.minLength(3),
            Validators.maxLength(100),
          ],
        ],
        phone: ['', Validators.required],
        profilePic: [null], // Ensure to handle file input separately
        agree: [false, Validators.requiredTrue],
        role: 'ROLE_CLIENT',
      },
      {
        validator: ConfirmPasswordValidator.MatchPassword,
      }
    );
  }

  onFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.fileToUpload = input.files[0];
    }
  }

  submit() {
    this.hasError = false;

    // Create a FormData object
    const formData = new FormData();

    // Add the JSON data
    const jsonData = {
      email: this.registrationForm.get('email')?.value,
      password: this.registrationForm.get('password')?.value,
      name: this.registrationForm.get('name')?.value,
      phone: this.registrationForm.get('phone')?.value,
      role: 'ROLE_CLIENT',
    };

    formData.append('json', JSON.stringify(jsonData));

    // Append the file if it exists
    if (this.fileToUpload) {
      formData.append('profilePic', this.fileToUpload, this.fileToUpload.name);
    }

    // Log the FormData for debugging
    formData.forEach((value, key) => {
      console.log(key, value);
    });

    // Make API request
    const registrationSubscr = this.authService
      .registerClient(formData)
      .pipe(first())
      .subscribe(
        (response) => {
          console.log('Registration successful:', response);
          localStorage.setItem('token', response.token);
          localStorage.setItem('refreshToken', response.refreshToken);
          this.router.navigate(['/']);
        },
        (error) => {
          console.error('Registration error:', error);
          this.hasError = true;
        }
      );
    this.unsubscribe.push(registrationSubscr);
  }
  


  ngOnDestroy() {
    this.unsubscribe.forEach((sb) => sb.unsubscribe());
  }
}
