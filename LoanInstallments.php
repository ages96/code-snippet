<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoanInstallments extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoanInstallment_model');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
    }

    public function index() {
        $cache_key = 'all_installments';

        // Try to get the data from the cache
        if (!$data['installments'] = $this->cache->get($cache_key)) {
            // If the data is not in the cache, get it from the model
            $data['installments'] = $this->LoanInstallment_model->get_all_installments();

            // Save the data in the cache for future requests
            $this->cache->save($cache_key, $data['installments'], 300); // Cache for 5 minutes
        }

        $this->load->view('loan_installments/index', $data);
    }

    public function view($id) {
        $data['installment'] = $this->LoanInstallment_model->get_installment($id);
        if (empty($data['installment'])) {
            show_404();
        }
        $this->load->view('loan_installments/view', $data);
    }

    public function create() {
        $this->form_validation->set_rules('loan_id', 'Loan ID', 'required|integer');
        $this->form_validation->set_rules('installment_amount', 'Installment Amount', 'required|numeric');
        $this->form_validation->set_rules('due_date', 'Due Date', 'required|callback_validate_date');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('loan_installments/create');
        } else {
            $this->LoanInstallment_model->create_installment();
            $this->cache->delete('all_installments'); // Clear the cache
            $this->session->set_flashdata('success', 'Installment created successfully');
            redirect('loaninstallments');
        }
    }

    public function update($id) {
        $data['installment'] = $this->LoanInstallment_model->get_installment($id);
        if (empty($data['installment'])) {
            show_404();
        }

        $this->form_validation->set_rules('installment_amount', 'Installment Amount', 'required|numeric');
        $this->form_validation->set_rules('due_date', 'Due Date', 'required|callback_validate_date');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('loan_installments/update', $data);
        } else {
            $this->LoanInstallment_model->update_installment($id);
            $this->cache->delete('all_installments'); // Clear the cache
            $this->session->set_flashdata('success', 'Installment updated successfully');
            redirect('loaninstallments');
        }
    }

    public function delete($id) {
        if (!$this->LoanInstallment_model->get_installment($id)) {
            show_404();
        }

        $this->LoanInstallment_model->delete_installment($id);
        $this->cache->delete('all_installments'); // Clear the cache
        $this->session->set_flashdata('success', 'Installment deleted successfully');
        redirect('loaninstallments');
    }

    public function validate_date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
