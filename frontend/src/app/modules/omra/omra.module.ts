import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OmraListComponent } from './omra-list/omra-list.component';
import { OmraDetailsComponent } from './omra-details/omra-details.component';
import { OmraComponent } from './omra.component';
import { PriceModalComponent } from './price-modal/price-modal.component';
import { ConfirmationDialogComponent } from './confirmation-dialog/confirmation-dialog.component';
import { MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { NgApexchartsModule } from 'ng-apexcharts';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { RouterModule, Routes } from '@angular/router';
import { OffersRoutingModule } from 'src/app/pages/offers/offers-routing.module';
import { WidgetsModule } from "../../_metronic/partials/content/widgets/widgets.module";
import { ModalFormComponent } from './modal-form/modal-form.component';
import { ModalsModule } from "../../_metronic/partials/layout/modals/modals.module";
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { FormsModule, ReactiveFormsModule } from '@angular/forms'; 
import { NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { MatIconModule } from '@angular/material/icon';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';

@NgModule({
  declarations: [
    OmraListComponent,
    OmraDetailsComponent,
    OmraComponent,
    ModalFormComponent,
    PriceModalComponent,
    ConfirmationDialogComponent
  ],
  imports: [
    CommonModule,
    NgApexchartsModule,
    InlineSVGModule,
    RouterModule,
    OffersRoutingModule,
    WidgetsModule,
    ModalsModule,
    NgbModule,
    FormsModule,
    NgbModalModule,
    MatIconModule,
    NgMultiSelectDropDownModule.forRoot(),
    MatFormFieldModule,
    MatSelectModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatButtonModule
  ],
  exports: [OmraComponent] // Export OmraComponent
})
export class OmraModule { }
