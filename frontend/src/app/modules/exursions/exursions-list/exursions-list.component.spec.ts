import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExursionsListComponent } from './exursions-list.component';

describe('ExursionsListComponent', () => {
  let component: ExursionsListComponent;
  let fixture: ComponentFixture<ExursionsListComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ExursionsListComponent]
    });
    fixture = TestBed.createComponent(ExursionsListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
