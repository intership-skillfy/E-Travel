import { Component, Input, OnInit } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-price-modal',
  templateUrl: './price-modal.component.html',
  styleUrls: ['./price-modal.component.scss']
})
export class PriceModalComponent implements OnInit {
  @Input() price: any;
  @Input() index: number;
  form: FormGroup;

  constructor(
    public activeModal: NgbActiveModal,
    private fb: FormBuilder
  ) {
    this.form = this.fb.group({
      start_date: [''],
      end_date: [''],
      hotels: [''],
      price: ['']
    });
  }

  ngOnInit() {
    if (this.price) {
      this.form.patchValue(this.price);
    }
  }

  onSave() {
    if (this.form.valid) {
      this.activeModal.close({ price: this.form.value, index: this.index });
    }
  }

  onCancel() {
    this.activeModal.dismiss();
  }
}
